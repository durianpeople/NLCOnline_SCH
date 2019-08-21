<?php

namespace NLC\Base;

use NLC\Throwable\InvalidLength;
use NLC\Throwable\QuestionsNotFound;
use Accounts;
use PuzzleError;
use NLC\Throwable\AccessDenied;
use DatabaseRowInput;
use NLC\Base\Sesi;

/**
 * Questions class
 * 
 * @property-read int $id
 * @property-read string $question_pdf_url
 * @property string $name
 */
class Questions
{
    private $id;
    private $name;

    #region Static
    public static function create(string $name)
    {
        if (!Accounts::authAccess(USER_AUTH_EMPLOYEE)) throw new AccessDenied;
        if (\Database::insert(
            "app_nlc_questions",
            [
                (new \DatabaseRowInput)
                    ->setField("name", $name)
            ]
        )) {
            return self::load(\Database::lastId());
        } else return null;
    }

    public static function load(int $id)
    {
        return new self($id);
    }
    #endregion

    private function __construct(int $id)
    {
        if (!Accounts::authAccess(USER_AUTH_REGISTERED)) throw new AccessDenied;
        if (!Accounts::authAccess(USER_AUTH_EMPLOYEE) && debug_backtrace()[3]['class'] != Sesi::class) throw new AccessDenied;
        if (null !== $data = \Database::getRow("app_nlc_questions", "id", $id)) {
            $this->id = $data['id'];
            $this->name = $data['name'];
        } else throw new QuestionsNotFound;
    }

    public function __set($name, $value)
    {
        switch ($name) {
            case "name":
                if (!Accounts::authAccess(USER_AUTH_EMPLOYEE)) throw new AccessDenied;
                $this->name = $value;
        }
    }

    public function __get($name)
    {
        switch ($name) {
            case "id":
                return $this->id;
            case "name":
                return $this->name;
            case "question_pdf_url":
                return \UserData::getURL("QUESTION_" . $this->id);
        }
    }

    public function uploadQuestionPDF($filename)
    {
        if (!Accounts::authAccess(USER_AUTH_EMPLOYEE)) throw new AccessDenied;
        if (\IO::exists($filename)) {
            if (!\UserData::move("QUESTION_" . $this->id, \IO::physical_path($filename), true)) {
                throw new \IOError("Cannot move PDF file");
            } else {
                return true;
            }
        }
    }

    public function addAnswerkey(int $number, int $answer)
    {
        if (!Accounts::authAccess(USER_AUTH_EMPLOYEE)) throw new AccessDenied;
        \Database::insert(
            "app_nlc_questions_answerkey",
            [
                (new DatabaseRowInput)
                    ->setField("id", $this->id)
                    ->setField("number", $number)
                    ->setField("answer", $answer)
            ]
        );
    }


    public function resetAnswerkey()
    {
        if (!Accounts::authAccess(USER_AUTH_EMPLOYEE)) throw new AccessDenied;
        \Database::delete("app_nlc_questions_answerkey", "id", $this->id);
    }
}
