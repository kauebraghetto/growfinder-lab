<?php
require_once 'config.php';
session_name(SESSION_NAME);
session_start();
if (empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}
if (time() - ($_SESSION['login_time'] ?? 0) > SESSION_TIMEOUT) {
    session_destroy();
    header('Location: index.php?timeout=1');
    exit;
}
$email = $_SESSION['usuario_email'] ?? '';
$page_title = 'Entendendo os LLMs — Growfinder Lab';
$guia_titulo = 'Guia de LLMs';
$guia_modulos = [
    'O que é um LLM',
    'Da internet ao conhecimento',
    'O motor por dentro',
    'Como o modelo aprende',
    'Por que se comporta assim',
    'O que vem a seguir'
];
$modulos_dir = 'modulos/llm/';
include 'guia-template.php';
