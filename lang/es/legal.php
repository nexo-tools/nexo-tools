<?php

// Legal pages (privacy + terms), rendered by legal/show.
//
// NOT reviewed by a lawyer. Written to describe accurately what this codebase
// actually does — which is the part an agent can get right — so that a review,
// if the owner wants one, starts from something true rather than from a
// template full of clauses about data the app never collects.
//
// Spanish is the source (the ecosystem is Spanish-first); en/pt are translations
// of this file, not independent texts.
return [
    'updated' => 'Última actualización: 28 de julio de 2026',

    // Rendered only when NEXO_LEGAL_OPERATOR / NEXO_LEGAL_CONTACT are set.
    'operator' => [
        'h' => 'Quién opera esta instancia',
        'p' => 'Esta instancia la opera :operator.',
        'contact' => 'Para cualquier consulta sobre tus datos puedes escribir a :contact.',
    ],

    'privacy' => [
        'title' => 'Privacidad',
        'intro' => 'Nexo Tools es la portada del ecosistema Nexo: desde aquí conoces las herramientas y entras a la que necesites. Es open source y self-hosted. Recogemos lo mínimo para que el hub funcione, y nada más. No usamos cookies de seguimiento, no hay analítica de terceros y no se envía información a redes publicitarias.',
        'sections' => [
            [
                'h' => 'Mirar el hub no necesita cuenta',
                'p' => 'La portada, el catálogo de herramientas, el centro de ayuda y estas mismas páginas son públicos: se ven sin registrarse y sin que guardemos nada tuyo.',
            ],
            [
                'h' => 'Qué guardamos de tu cuenta',
                'p' => 'Solo si decidís crear una: nombre, email y una versión cifrada (hash) de la contraseña. El email se usa para identificarte al entrar y para mandarte el enlace de recuperación si olvidás la clave. No pedimos teléfono, dirección ni datos de pago: el hub no cobra nada.',
            ],
            [
                'h' => 'Tu panel "tus herramientas"',
                'p' => 'Cuando agregas una herramienta a tu panel guardamos dos cosas: tu cuenta y el identificador de esa herramienta en el catálogo (por ejemplo "nexoagenda"), con la fecha en que la agregaste. Es una lista de accesos directos y nada más: el hub no le pide datos a las otras herramientas ni sabe qué haces dentro de ellas, y tu panel solo lo ves tú.',
            ],
            [
                'h' => 'Entrar con Nexo ID',
                'p' => 'Es opcional. Si lo usas, Nexo ID nos entrega tu identificador de cuenta, tu nombre y tu email, y guardamos ese identificador para reconocerte la próxima vez. También puedes usar el hub con una cuenta local, sin Nexo ID.',
            ],
            [
                'h' => 'Métricas del ecosistema, sin cookies',
                'p' => 'Este hub cuenta las visitas de las herramientas Nexo y de alvarocdev.com. De cada visita se guarda la herramienta que la emitió, la ruta visitada sin la parte que va después del "?", el día, el país si la red lo indica, y desde qué herramienta llegaste. Tu IP y tu navegador no se guardan: se usan al vuelo para derivar una huella anónima que incluye la fecha, así que la huella de hoy no se puede comparar con la de mañana ni cruzar con la de otro sitio. No se instala ninguna cookie para medir, y si tu navegador envía "Do Not Track" o "Global Privacy Control" no se guarda nada. Quien opera la instancia ve totales por día, por herramienta y las rutas más vistas, nunca visitas individuales. Además la medición es opcional por instalación y viene apagada de fábrica.',
            ],
            [
                'h' => 'Cookies',
                'p' => 'Solo las necesarias para que la web funcione: la de sesión (para mantenerte identificado si tienes cuenta) y las que recuerdan el idioma y el tema claro/oscuro que elegiste. Estas dos últimas se comparten con las demás herramientas Nexo del mismo dominio, para que tu elección te siga de una a otra. Ninguna sirve para publicidad ni para seguimiento.',
            ],
            [
                'h' => 'Correos',
                'p' => 'El hub solo envía el correo de recuperación de contraseña. Se entrega a través de un proveedor de email externo, que necesariamente procesa la dirección de destino y el contenido del mensaje para poder entregarlo.',
            ],
            [
                'h' => 'Cuánto tiempo',
                'p' => 'Tu cuenta y su panel se conservan mientras la cuenta exista; al borrarla se borra con ella la lista de herramientas que agregaste. Los recuentos de visitas no están asociados a ninguna cuenta y quedan como cifras por día y por herramienta.',
            ],
            [
                'h' => 'Tus derechos',
                'p' => 'Podés pedir acceso a tus datos, su corrección o su borrado escribiendo a quien opera esta instancia (el contacto está en la página de ayuda).',
            ],
            [
                'h' => 'Otras instancias',
                'p' => 'Nexo Tools se puede instalar en cualquier servidor. Cada instalación es independiente y responsable de sus propios datos: esta política habla solo de esta instancia. Cada herramienta del catálogo tiene además su propia política.',
            ],
        ],
    ],

    'terms' => [
        'title' => 'Términos de uso',
        'intro' => 'Al usar esta instancia de Nexo Tools aceptás lo que sigue. Es un servicio gratuito, ofrecido tal cual está.',
        'sections' => [
            [
                'h' => 'Qué es el servicio',
                'p' => 'Un punto de entrada al ecosistema Nexo: te muestra qué hace cada herramienta y te lleva a ella. Con una cuenta, además, puedes armar un panel con las que usas. El hub no presta el servicio de cada herramienta: cada una tiene su propio operador, sus términos y su política de privacidad.',
            ],
            [
                'h' => 'Tu cuenta',
                'p' => 'La cuenta es opcional: solo hace falta para el panel "tus herramientas". Puedes crearla aquí o entrar con Nexo ID. Eres responsable de lo que pase con tu cuenta y de mantener tu contraseña a salvo.',
            ],
            [
                'h' => 'Las herramientas del catálogo',
                'p' => 'Los enlaces del catálogo apuntan a herramientas del ecosistema alojadas por separado. Que aparezcan aquí no nos hace responsables de su funcionamiento, su disponibilidad ni del uso que hagas de ellas.',
            ],
            [
                'h' => 'Uso indebido',
                'p' => 'No se permite automatizar registros, sobrecargar el servicio, ni intentar acceder a cuentas o datos ajenos. Quien opera esta instancia puede limitar el acceso o dar de baja una cuenta que haga cualquiera de esas cosas.',
            ],
            [
                'h' => 'Métricas del ecosistema',
                'p' => 'Si esta instancia tiene la medición activada, recibe recuentos de visitas de las herramientas del ecosistema. Solo se aceptan datos de las herramientas que figuran en el catálogo, y en ningún caso incluyen datos personales.',
            ],
            [
                'h' => 'Disponibilidad',
                'p' => 'El servicio se ofrece sin garantías de disponibilidad. Hacemos lo razonable para que esté en línea, pero puede haber interrupciones. Que el hub esté caído no impide entrar a cada herramienta por su propia dirección.',
            ],
            [
                'h' => 'Límite de responsabilidad',
                'p' => 'Quien opera esta instancia no se hace responsable de daños derivados del uso del servicio, incluidas pérdidas de datos.',
            ],
            [
                'h' => 'Software libre',
                'p' => 'Nexo Tools se distribuye con licencia MIT: puedes leer el código, modificarlo y alojar tu propia instancia. El software se entrega sin garantías, según indica esa licencia.',
            ],
            [
                'h' => 'Cambios',
                'p' => 'Estos términos pueden cambiar. La fecha de arriba indica la última actualización.',
            ],
        ],
    ],
];
