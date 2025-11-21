<?php if (session_status() == PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>RACE FOR GLORY</title>
  <!-- Tailwind -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="/rfg/assets/JavaScript/admin-panel.js?v=1"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            dark: '#0b0b0c',
            primary: '#C9A300'
          }
        }
      }
    }
  </script>

  <!-- Seu CSS opcional -->
  <link href="/rfg/assets/css/main.css" rel="stylesheet">
  <link href="/rfg/assets/css/dashboard_adm.css" rel="stylesheet">
  <link href="/rfg/assets/css/temporada.css" rel="stylesheet">
  <link href="/rfg/assets/css/punicao.css" rel="stylesheet">
</head>

<body class="bg-neutral-50 text-neutral-900">