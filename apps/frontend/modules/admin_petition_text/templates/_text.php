<?php 
use_helper('Text');
echo simple_format_text(truncate_text($petition_text['body'] . $petition_text['email_body'], 400));