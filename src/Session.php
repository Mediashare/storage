<?php

namespace Kzu\Storage;

use DateTime;
use Kzu\Database\DatabaseQuery;

Trait Session {
    static public $session_lifetime = 604800; // 7 days

    static public function getSession() {
        if (!session_id()):
            session_start();
        endif;

        $session = DatabaseQuery::findOneBy('sessions', ['id' => session_id()], false);
        if (!$session):
            if ($lifetime = Session::$session_lifetime):
                $expireAt = new DateTime($lifetime);
            endif;
            DatabaseQuery::insert('sessions', [$session = ['id' => session_id(), 'createAt' => new DateTime(), 'expireAt' => $expireAt ?? null]]);
        endif;

        return $session;
    }

    static public function set(string $key, $value) {
        $session = Session::getSession();
        DatabaseQuery::update('sessions', ['id' => $session['id']], [$key => $value]);
        return Session::get($key) ?? null;
    }
    
    static public function get(string $key) {
        return Session::getSession()[$key] ?? null;
    }

}