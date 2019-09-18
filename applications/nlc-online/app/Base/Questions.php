<?php

namespace NLC\Base;

use NLC\Throwable\InvalidLength;
use NLC\Throwable\QuestionsNotFound;
use Accounts;
use PuzzleError;
use NLC\Throwable\AccessDenied;
use DatabaseRowInput;
use NLC\Base\Sesi;
use NLC\Throwable\QuestionsIsPermanent;
use PuzzleUser;

/**
 * Questions class
 * 
 * @property-read int $id
 * @property string $name
 */
class Questions implements \JsonSerializable
{
    private $id;
    private $name;

    #region Static
    public static function create(string $name)
    {
        if (!PuzzleUser::isAccess(USER_AUTH_EMPLOYEE)) throw new AccessDenied;
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

    /**
     * Get list of all questions
     * @return Questions[]
     */
    public static function list(): array
    {
        $ss = [];
        $db = \Database::execute("SELECT id FROM app_nlc_questions");
        while ($row = $db->fetch_assoc()) {
            $ss[] = new self($row['id']);
        }
        return $ss;
    }
    #endregion

    private function __construct(int $id)
    {
        if (!PuzzleUser::isAccess(USER_AUTH_REGISTERED)) throw new AccessDenied;
        if (!PuzzleUser::isAccess(USER_AUTH_EMPLOYEE) && debug_backtrace()[3]['class'] != Sesi::class) throw new AccessDenied;
        if (null !== $data = \Database::getRow("app_nlc_questions", "id", $id)) {
            $this->id = (int) $data['id'];
            $this->name = $data['name'];
        } else throw new QuestionsNotFound;
    }

    public function __set($name, $value)
    {
        switch ($name) {
            case "name":
                if (!PuzzleUser::isAccess(USER_AUTH_EMPLOYEE)) throw new AccessDenied;
                $this->name = $value;
                \Database::update("app_nlc_questions", (new \DatabaseRowInput)->setField("name", $value), "id", $this->id);
                break;
        }
    }

    public function __get($name)
    {
        switch ($name) {
            case "id":
                return $this->id;
            case "name":
                return $this->name;
        }
    }

    public function uploadQuestionPDF($input_name)
    {
        if ($this->hasPDF()) throw new QuestionsIsPermanent;
        if (!PuzzleUser::isAccess(USER_AUTH_EMPLOYEE)) throw new AccessDenied;
        if (!\UserData::move_uploaded("QUESTION_" . $this->id, $input_name, true)) throw new \IOError("Cannot move PDF file");
        return true;
    }

    public function hasPDF(): bool
    {
        return \UserData::exists("QUESTION_" . $this->id);
    }

    public function uploadAnswerKey($csv_file)
    {
        if (!PuzzleUser::isAccess(USER_AUTH_EMPLOYEE)) throw new AccessDenied;
        $this->resetAnswerkey();
        $csvFile = file($csv_file);
        $payload = [];
        foreach ($csvFile as $line) {
            $data = str_getcsv($line);
            $payload[] = (new DatabaseRowInput)
                ->setField("question_id", $this->id)
                ->setField("number", $data[0])
                ->setField("answer", array_search($data[1], [0 => "A", 1 => "B", 2 => "C", 3 => "D", 4 => "E"]));
        }
        \Database::insert("app_nlc_questions_answerkey", $payload);
    }

    private function resetAnswerkey()
    {
        if (!PuzzleUser::isAccess(USER_AUTH_EMPLOYEE)) throw new AccessDenied;
        \Database::delete("app_nlc_questions_answerkey", "question_id", $this->id);
    }

    public function getAnswerKey()
    {
        if (!PuzzleUser::isAccess(USER_AUTH_EMPLOYEE)) throw new AccessDenied;
        $db = \Database::execute("SELECT `number`, `answer` FROM app_nlc_questions_answerkey WHERE question_id = '?'", $this->id);
        $ak = [];
        while ($row = $db->fetch_assoc()) {
            $ak[$row["number"]] = $row['answer'];
        }
        return $ak;
    }

    /**
     * @return int
     */
    public function jumlahsoal() {
        $db = \Database::execute("SELECT COUNT(1) FROM app_nlc_questions_answerkey WHERE question_id = '?'", $this->id);
        return (int) $db->fetch_row()[0];
    }

    public function jsonSerialize()
    {
        if (PuzzleUser::isAccess(USER_AUTH_EMPLOYEE)) {
            return [
                "id" => $this->id,
                "name" => $this->name,
                "question_num" => $this->jumlahsoal(),
                "answer_key" => $this->getAnswerKey(),
                "hasPDF" => $this->hasPDF(),
            ];
        } else {
            return [
                "id" => $this->id,
                "name" => $this->name,
                "question_num" => $this->jumlahsoal()
            ];
        }
    }
}
