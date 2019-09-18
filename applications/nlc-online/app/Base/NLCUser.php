<?php

namespace NLC\Base;

use PuzzleUser;

class NLCUser extends PuzzleUser
{
    private static $singleton = [];

    private $nlc_id;

    public function __get($name)
    {
        switch ($name) {
            case "nlc_id":
                return $this->nlc_id;
            default:
                return parent::__get($name);
        }
    }

    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            "nlc_id" => $this->nlc_id
        ]);
    }

    /**
     * @return self[]
     */
    public static function getList(int $limit = null, int $start = null)
    {
        $u = [];
        $db = \Database::execute("SELECT `nlc_id` FROM app_nlc_user_nlc_id");
        while ($row = $db->fetch_assoc()) {
            try {
                $u[] = self::get($row['nlc_id']);
            } catch (\Exception $e) { }
        }
        return $u;
    }

    /**
     * 
     *
     * @param string $password
     * @param string $tim_name
     * @param string $email
     * @param string $nlc_id
     * @return self
     */
    public static function create(string $password, string $tim_name, string $email, string $nlc_id)
    {
        return \Database::transaction(function () use ($password, $tim_name, $email, $nlc_id) {
            $user = parent::create(
                $password,
                $tim_name,
                $email,
                null
            );
            \Database::insert("app_nlc_user_nlc_id", [
                (new \DatabaseRowInput)
                    ->setField("user_id", $user->id)
                    ->setField("nlc_id", $nlc_id)
            ]);
            return NLCUser::get($nlc_id);
        });
    }

    public function delete(){
        throw new \PuzzleError("Cannot delete NLC User!");
    }

    public static function getById(int $id){
        $nlc_id = \Database::read("app_nlc_user_nlc_id", "nlc_id", "user_id", $id);
        return self::get($nlc_id);
    }

    public static function get(string $nlc_id)
    {
        return self::$singleton[$nlc_id] ?? self::$singleton[$nlc_id] = new self($nlc_id);
    }

    protected function __construct(string $nlc_id)
    {
        $userid = \Database::read("app_nlc_user_nlc_id", "user_id", "nlc_id", $nlc_id);
        if (empty($userid)) {
            throw new \PuzzleError("User NLC not found!");
        } else {
            parent::__construct((int) $userid);
            $this->nlc_id = $nlc_id;
        }
    }
}
