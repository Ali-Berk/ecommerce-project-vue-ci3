<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config = array(
    'protocol' => 'smtp',
    'smtp_host' => 'smtp.gmail.com',
    'smtp_user' => 'example@example.com',
    'smtp_pass' => 'example app password',
    'smtp_port' => 587,
    'smtp_crypto' => 'tls',
    'mailtype' => 'html',
    'charset' => 'utf-8',
    'wordwrap' => TRUE,
    'newline' => "\r\n"
);
