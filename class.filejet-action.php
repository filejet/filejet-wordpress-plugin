<?php

class Filejet_Action
{
    const ENTER_KEY = 'enter-key';
    const ADD_MUTATION_SETTING = 'add-mutation-setting';
    const ADD_IGNORE_SETTING = 'add-ignore-setting';
    const ADD_LAZY_LOAD_SETTING = 'add-lazy-load-setting';
    const DELETE_MUTATION_SETTING = 'delete-mutation-setting';
    const DELETE_IGNORE_SETTING = 'delete-ignore-setting';
    const DELETE_LAZY_LOAD_SETTING = 'delete-lazy-load-setting';


    public static function validate($action = ''): string
    {
        $allowedActions = [
            self::ENTER_KEY,
            self::ADD_MUTATION_SETTING,
            self::ADD_IGNORE_SETTING,
            self::ADD_LAZY_LOAD_SETTING,
            self::DELETE_MUTATION_SETTING,
            self::DELETE_IGNORE_SETTING,
            self::DELETE_LAZY_LOAD_SETTING
        ];

        if (!empty($action) && in_array($action, $allowedActions)) {
            return $action;
        }

        return '';
    }
}
