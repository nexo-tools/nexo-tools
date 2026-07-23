<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <?php echo $__env->make('partials.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </head>
    <body class="bg-brand-50 font-sans text-ink antialiased dark:bg-slate-900 dark:text-slate-200">
        <main id="contenido" class="mx-auto max-w-4xl px-4 py-12">
            <header class="mb-10 text-center">
                <span class="text-3xl font-bold tracking-tight"><?php echo e(config('app.name')); ?></span>
                <p class="mt-2 text-slate-600 dark:text-slate-400"><?php echo e(__('Todas las herramientas Nexo, una sola cuenta.')); ?></p>
            </header>

            <div class="grid gap-4 sm:grid-cols-2">
                <?php $__currentLoopData = $tools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tool): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-800">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold"><?php echo e($tool['name']); ?></h2>
                            <span class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                'rounded-full px-2 py-0.5 text-xs font-medium',
                                'bg-green-100 text-green-800' => $tool['status'] === 'active',
                                'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300' => $tool['status'] !== 'active',
                            ]); ?>"><?php echo e($tool['status'] === 'active' ? __('Activa') : __('Próximamente')); ?></span>
                        </div>
                        <p class="mt-1 text-sm text-slate-600 dark:text-slate-400"><?php echo e($tool['tagline']); ?></p>
                        <?php if($tool['status'] === 'active' && $tool['url']): ?>
                            <a href="<?php echo e($tool['url']); ?>" class="mt-3 inline-block rounded-lg bg-brand-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-brand-700"><?php echo e(__('Usar')); ?></a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <section class="mt-12 rounded-2xl bg-slate-100 p-5 text-center dark:bg-slate-800/50">
                <p class="text-sm text-slate-600 dark:text-slate-400">
                    <?php echo e(__('¿Eres desarrollador?')); ?>

                    <a href="<?php echo e($githubOrg); ?>" class="font-medium text-brand-700 hover:underline dark:text-brand-400"><?php echo e(__('Explora el código en GitHub')); ?></a>
                </p>
            </section>

            <?php if(config('nexo.attribution_text')): ?>
                <footer class="mt-10 text-center text-xs text-slate-400">
                    <?php if(config('nexo.attribution_url')): ?>
                        <a href="<?php echo e(config('nexo.attribution_url')); ?>" class="hover:underline"><?php echo e(config('nexo.attribution_text')); ?></a>
                    <?php else: ?>
                        <?php echo e(config('nexo.attribution_text')); ?>

                    <?php endif; ?>
                </footer>
            <?php endif; ?>

            <?php if (isset($component)) { $__componentOriginal7b69d71eac2771bb6249ff4d7cc262ee = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7b69d71eac2771bb6249ff4d7cc262ee = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.locale-switcher','data' => ['class' => 'mt-6 flex justify-center']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('locale-switcher'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mt-6 flex justify-center']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7b69d71eac2771bb6249ff4d7cc262ee)): ?>
<?php $attributes = $__attributesOriginal7b69d71eac2771bb6249ff4d7cc262ee; ?>
<?php unset($__attributesOriginal7b69d71eac2771bb6249ff4d7cc262ee); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7b69d71eac2771bb6249ff4d7cc262ee)): ?>
<?php $component = $__componentOriginal7b69d71eac2771bb6249ff4d7cc262ee; ?>
<?php unset($__componentOriginal7b69d71eac2771bb6249ff4d7cc262ee); ?>
<?php endif; ?>
        </main>
    </body>
</html>
<?php /**PATH /app/resources/views/home.blade.php ENDPATH**/ ?>