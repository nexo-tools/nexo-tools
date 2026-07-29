<?php

// Páginas legais (privacidade + termos). Fonte: lang/es/legal.php.
// NÃO revisado por advogado: escrito para descrever com precisão o que este
// código realmente faz.
return [
    'updated' => 'Última atualização: 28 de julho de 2026',

    'operator' => [
        'h' => 'Quem opera esta instância',
        'p' => 'Esta instância é operada por :operator.',
        'contact' => 'Para qualquer dúvida sobre os seus dados você pode escrever para :contact.',
    ],

    'privacy' => [
        'title' => 'Privacidade',
        'intro' => 'O Nexo Tools é a porta de entrada do ecossistema Nexo: aqui você conhece as ferramentas e vai até a que precisa. É open source e self-hosted. Coletamos o mínimo necessário para o hub funcionar, e nada além disso. Sem cookies de rastreamento, sem análise de terceiros e sem envio de dados a redes de publicidade.',
        'sections' => [
            [
                'h' => 'Navegar pelo hub não exige conta',
                'p' => 'A página inicial, o catálogo de ferramentas, a central de ajuda e estas próprias páginas são públicos: podem ser vistos sem cadastro e sem que guardemos nada sobre você.',
            ],
            [
                'h' => 'O que guardamos da sua conta',
                'p' => 'Só se você decidir criar uma: nome, e-mail e uma versão cifrada (hash) da senha. O e-mail é usado para identificar você ao entrar e para enviar o link de recuperação se esquecer a senha. Não pedimos telefone, endereço nem dados de pagamento: o hub não cobra nada.',
            ],
            [
                'h' => 'O seu painel "suas ferramentas"',
                'p' => 'Quando você adiciona uma ferramenta ao seu painel guardamos duas coisas: a sua conta e o identificador dessa ferramenta no catálogo (por exemplo "nexoagenda"), com a data em que você a adicionou. É uma lista de atalhos e nada mais: o hub não pede dados às outras ferramentas nem sabe o que você faz dentro delas, e só você vê o seu painel.',
            ],
            [
                'h' => 'Entrar com o Nexo ID',
                'p' => 'É opcional. Se você usar, o Nexo ID nos entrega o seu identificador de conta, o seu nome e o seu e-mail, e guardamos esse identificador para reconhecer você na próxima vez. Você também pode usar o hub com uma conta local, sem o Nexo ID.',
            ],
            [
                'h' => 'Métricas do ecossistema, sem cookies',
                'p' => 'Este hub conta as visitas das ferramentas Nexo e do alvarocdev.com. De cada visita guardamos a ferramenta que a emitiu, o caminho visitado sem a parte que vem depois do "?", o dia, o país se a rede informar, e de qual ferramenta você veio. O seu IP e o seu navegador não são guardados: são usados na hora para derivar uma impressão anônima que inclui a data, então a impressão de hoje não pode ser comparada com a de amanhã nem cruzada com a de outro site. Nenhum cookie é instalado para medir, e se o seu navegador enviar "Do Not Track" ou "Global Privacy Control" nada é guardado. Quem opera a instância vê totais por dia, por ferramenta e os caminhos mais vistos, nunca visitas individuais. Além disso, a medição é opcional por instalação e vem desligada de fábrica.',
            ],
            [
                'h' => 'Cookies',
                'p' => 'Apenas os necessários para o site funcionar: o de sessão (para manter você identificado se tiver conta) e os que lembram o idioma e o tema claro/escuro escolhidos. Estes dois últimos são compartilhados com as demais ferramentas Nexo do mesmo domínio, para que a sua escolha acompanhe você de uma para outra. Nenhum serve para publicidade ou rastreamento.',
            ],
            [
                'h' => 'E-mails',
                'p' => 'O hub envia apenas o e-mail de recuperação de senha. Ele é entregue por um provedor de e-mail externo, que necessariamente processa o endereço de destino e o conteúdo da mensagem para poder entregá-lo.',
            ],
            [
                'h' => 'Por quanto tempo',
                'p' => 'A sua conta e o seu painel são mantidos enquanto a conta existir; ao apagá-la, a lista de ferramentas que você adicionou some junto. As contagens de visitas não estão associadas a nenhuma conta e ficam como números por dia e por ferramenta.',
            ],
            [
                'h' => 'Os seus direitos',
                'p' => 'Você pode pedir acesso aos seus dados, a sua correção ou a sua exclusão escrevendo a quem opera esta instância (o contato está na página de ajuda).',
            ],
            [
                'h' => 'Outras instâncias',
                'p' => 'O Nexo Tools pode ser instalado em qualquer servidor. Cada instalação é independente e responsável pelos seus próprios dados: esta política fala apenas desta instância. Cada ferramenta do catálogo também tem a sua própria política.',
            ],
        ],
    ],

    'terms' => [
        'title' => 'Termos de uso',
        'intro' => 'Ao usar esta instância do Nexo Tools você aceita o que segue. É um serviço gratuito, oferecido como está.',
        'sections' => [
            [
                'h' => 'O que é o serviço',
                'p' => 'Um ponto de entrada para o ecossistema Nexo: mostra o que cada ferramenta faz e leva você até ela. Com uma conta, você também pode montar um painel com as que usa. O hub não presta o serviço de cada ferramenta: cada uma tem o seu próprio operador, os seus termos e a sua política de privacidade.',
            ],
            [
                'h' => 'A sua conta',
                'p' => 'A conta é opcional: só é necessária para o painel "suas ferramentas". Você pode criá-la aqui ou entrar com o Nexo ID. Você é responsável pelo que acontecer com a sua conta e por manter a sua senha em segurança.',
            ],
            [
                'h' => 'As ferramentas do catálogo',
                'p' => 'Os links do catálogo apontam para ferramentas do ecossistema hospedadas separadamente. O fato de aparecerem aqui não nos torna responsáveis pelo funcionamento delas, pela sua disponibilidade nem pelo uso que você fizer delas.',
            ],
            [
                'h' => 'Uso indevido',
                'p' => 'Não é permitido automatizar cadastros, sobrecarregar o serviço nem tentar acessar contas ou dados de terceiros. Quem opera esta instância pode limitar o acesso ou encerrar uma conta que faça qualquer uma dessas coisas.',
            ],
            [
                'h' => 'Métricas do ecossistema',
                'p' => 'Se esta instância tiver a medição ativada, ela recebe contagens de visitas das ferramentas do ecossistema. Só são aceitos dados das ferramentas que constam no catálogo, e em nenhum caso incluem dados pessoais.',
            ],
            [
                'h' => 'Disponibilidade',
                'p' => 'O serviço é oferecido sem garantias de disponibilidade. Fazemos o razoável para mantê-lo no ar, mas pode haver interrupções. O hub estar fora não impede que você acesse cada ferramenta pelo endereço dela.',
            ],
            [
                'h' => 'Limite de responsabilidade',
                'p' => 'Quem opera esta instância não se responsabiliza por danos decorrentes do uso do serviço, incluindo perdas de dados.',
            ],
            [
                'h' => 'Software livre',
                'p' => 'O Nexo Tools é distribuído sob a licença MIT: você pode ler o código, modificá-lo e hospedar a sua própria instância. O software é entregue sem garantias, conforme indica essa licença.',
            ],
            [
                'h' => 'Mudanças',
                'p' => 'Estes termos podem mudar. A data acima indica a última atualização.',
            ],
        ],
    ],
];
