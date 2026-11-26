// Alpine components for the Nexo shared chrome. Import this once in the tool's
// app.js (before Alpine.start()); it registers on `alpine:init`.
//
//   import './nexo-ui.js';   // then Alpine.start()
//
// The head theme-init snippet (partials/theme-init.blade.php) must run first so
// <html data-theme> is always stamped before Alpine reads it.

document.addEventListener('alpine:init', () => {
  // Light/dark toggle. The explicit choice persists in the `nexo-theme` cookie,
  // scoped to the parent domain so it crosses every ecosystem tool (dark in one =
  // dark in all), and wins over the OS preference (tokens.css keys dark off
  // [data-theme] and prefers-*). theme-init.blade.php reads that cookie on load.
  Alpine.data('nexoTheme', () => ({
    dark: document.documentElement.getAttribute('data-theme') === 'dark',
    toggle() {
      this.dark = !this.dark;
      const mode = this.dark ? 'dark' : 'light';
      document.documentElement.setAttribute('data-theme', mode);
      // On *.alvarocdev.com, scope the cookie to the parent domain; on localhost
      // the regex doesn't match, so it stays host-only and dev is unaffected.
      const domain = /(^|\.)alvarocdev\.com$/.test(location.hostname) ? '; domain=.alvarocdev.com' : '';
      try { document.cookie = 'nexo-theme=' + mode + '; path=/; max-age=31536000; SameSite=Lax' + domain; } catch (e) { /* private mode */ }
    },
  }));

  // Generic dropdown (app-switcher, locale, account). Closes on outside click
  // and Escape; the trigger owns aria-expanded.
  //
  // The panels declare role="menu"/"menuitem", and that role is a promise: arrow
  // navigation, Home/End, and focus returning to the trigger on close. Without
  // it a screen-reader user is told to press arrows and nothing happens.
  // Wire it up with:  x-data="nexoMenu" @keydown="onKeydown($event)"
  //
  // Modal mode (chrome v2, the phone sheet): x-data="nexoMenu({ modal: true })".
  // Same open/close/Escape/focus-return contract, but it is a dialog, not a menu:
  // Tab and Shift+Tab cycle INSIDE the panel (focus trap) instead of closing it,
  // every focusable counts (links, buttons, the theme toggle), and the page under
  // it stops scrolling. The trigger is the element with x-ref="trigger" (or the
  // first [aria-haspopup]); the panel is x-ref="panel".
  Alpine.data('nexoMenu', ({ modal = false } = {}) => ({
    open: false,
    modal,
    trigger() {
      return this.$refs.trigger || this.$el.querySelector('[aria-haspopup]');
    },
    toggle() {
      if (this.open) { this.close(); return; }
      this.open = true;
      if (this.modal) document.documentElement.classList.add('nexo-sheet-open');
      this.$nextTick(() => this.focusItem(0));
    },
    close({ restoreFocus = true } = {}) {
      if (!this.open) return;
      this.open = false;
      if (this.modal) document.documentElement.classList.remove('nexo-sheet-open');
      if (restoreFocus) this.trigger()?.focus();
    },
    items() {
      const root = this.modal ? (this.$refs.panel || this.$el) : this.$el;
      const selector = this.modal
        ? 'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])'
        : '[role="menuitem"]';
      return Array.from(root.querySelectorAll(selector))
        .filter((el) => !el.hasAttribute('disabled') && el.getClientRects().length > 0);
    },
    focusItem(index) {
      const items = this.items();
      if (!items.length) return;
      // Wrap around: past the end returns to the first item, and vice versa.
      items[(index + items.length) % items.length].focus();
    },
    moveFocus(step) {
      const items = this.items();
      const current = items.indexOf(document.activeElement);
      this.focusItem(current === -1 ? (step > 0 ? 0 : items.length - 1) : current + step);
    },
    onKeydown(event) {
      if (event.key === 'Escape') {
        if (this.open) event.stopPropagation();
        this.close();
        return;
      }
      if (!this.open) return;
      if (this.modal) {
        // Focus trap: the sheet covers the page, so focus must not leave it.
        if (event.key !== 'Tab') return;
        event.preventDefault();
        this.moveFocus(event.shiftKey ? -1 : 1);
        return;
      }
      const handlers = {
        ArrowDown: () => this.moveFocus(1),
        ArrowUp: () => this.moveFocus(-1),
        Home: () => this.focusItem(0),
        End: () => this.focusItem(this.items().length - 1),
        // Tabbing out of a menu closes it, but the browser keeps the focus move.
        Tab: () => this.close({ restoreFocus: false }),
      };
      const handler = handlers[event.key];
      if (!handler) return;
      if (event.key !== 'Tab') event.preventDefault();
      handler();
    },
  }));
});
