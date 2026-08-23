# TeatroSVicente

Site institucional do TeatroSVicente, desenvolvido em PHP, HTML, CSS e JavaScript.

## Descricao

O projeto esta organizado como um site simples com paginas PHP reutilizando um cabecalho e um rodape comuns. A navegacao principal inclui:

- `Main`
- `Contactos`
- `Agenda`

O layout inclui estilos globais, estilos por pagina e comportamento JavaScript para o menu responsivo em dispositivos moveis.

## Tecnologias

- PHP
- HTML5
- CSS3
- JavaScript
- Google Fonts: `Cinzel` e `Montserrat`

## Estrutura do projeto

```text
.
├── index.php
├── pages/
│   ├── home.php
│   ├── contactos.php
│   └── agenda.php
├── templates/
│   ├── header.php
│   └── footer.php
├── css/
│   ├── base.css
│   ├── components.css
│   ├── home.css
│   ├── contactos.css
│   ├── agenda.css
│   └── responsive.css
├── js/
│   └── main.js
└── img/
```

## Como correr localmente

E necessario ter o PHP instalado.

Na raiz do projeto, correr:

```bash
php -S 127.0.0.1:8000
```

Depois abrir no browser:

```text
http://127.0.0.1:8000
```

O ficheiro `index.php` redireciona automaticamente para `pages/home.php`.

## Como adicionar uma nova pagina

1. Criar um novo ficheiro em `pages/`.
2. Definir as variaveis da pagina antes de incluir o template:

```php
<?php
$pageTitle = 'Titulo | TeatroSVicente';
$activePage = 'nome-da-pagina';
$basePath = '../';
$pageCss = ['ficheiro.css'];

require_once __DIR__ . '/../templates/header.php';
?>

<main>
</main>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
```

3. Criar o CSS correspondente em `css/`, se necessario.
4. Adicionar o link de navegacao em `templates/header.php`.

## Personalizacao

As variaveis principais de cores, fontes e largura do conteudo estao em `css/base.css`.

O cabecalho, navegacao, menu mobile e rodape estao em `css/components.css` e `css/responsive.css`.

## Assets

As imagens do projeto devem ser colocadas em `img/`. O logo usado no cabecalho e no rodape e `img/pfp.jpg`.
