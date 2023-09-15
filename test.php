<?php

trait HasCaching
{
    protected static $cachingSchema = [
        [
            "name" => "caching_period",
            "type" => "number",
            "required" => false,
            "desc" => "How long should a message response be cached, in minutes? If the same message is submitted again, the response will be retrieved from the cache if available. Set to 0 to disable caching.",
            "default" => 1440,
            "isApiOption" => true,
        ],
        [
            "name" => "caching_scope",
            "type" => "select",
            "required" => false,
            "desc" => "Is the caching scope set per session, or is it global (across all sessions)? set 'session' for individual session-based caching or 'global' for caching that spans across all sessions.",
            "default" => "session",
            "isApiOption" => false,
            "options"=>[
                'session' => 'Per Session',
                'global' => 'Global'
            ]
        ],
        [
            "name" => "clear_cache",
            "type" => "boolean",
            "required" => false,
            "desc" => "Clear cache for the specified user_message and return an answer.",
            "isApiOption" => true,

        ]
    ];

}

class ClassA {
    use HasCaching;


    public function getOptions() {
        return static::$cachingSchema;
    }
}

$classA = new ClassA();
print_r($classA->getOptions());
