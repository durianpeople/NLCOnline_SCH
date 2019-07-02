<?php

namespace NLC\Base;

use NLC\Throwable\InvalidLength;
use NLC\Throwable\QuestionsNotFound;

class Questions
{
    private $handle;
    private $question = [];
    private $answer_key = [];
    private const TABLE = "app_nlc_questions";

    public function __construct(string $handle)
    {
        if (null !== $data = \Database::getRow(self::TABLE, "handle", $handle)) {
            $this->handle = $data['handle'];
            $this->question = json_decode($data['question_json']);
            $this->answer_key = json_decode($data['answer_key_json']);
        } else throw new QuestionsNotFound;
    }

    public function getHandle(): string
    {
        return $this->handle;
    }

    public function addQuestion(string $description, array $options, int $answer): bool
    {
        $this->question[] = [
            $description,
            $options
        ];
        $this->answer_key[] = $answer;
        return $this->commit();
    }

    public function deleteQuestion(int $id): bool
    {
        array_splice($this->question, $id, 1);
        array_splice($this->answer_key, $id, 1);
        return $this->commit();
    }

    public function getQuestionJSON(): string
    {
        return json_encode($this->question);
    }

    /**
     * 
     * @param string $answer JSON-formatted
     * @return float
     */
    public function judgeAnswer(string $answer): float
    {
        $diff = array_diff_assoc($this->answer_key, json_decode($answer));
        return (float) 1 - (sizeof($diff) / sizeof($this->answer_key));
    }

    private function commit(): bool
    {
        if (\Database::update(
            self::TABLE,
            (new \DatabaseRowInput)
                ->setField("question_json", json_encode($this->question))
                ->setField("answer_key_json", json_encode($this->answer_key)),
            "handle",
            $this->handle
        )) return true;
        else return false;
    }

    public static function create(string $handle): bool
    {
        if (strlen($handle) > 25) throw new InvalidLength;
        if (\Database::insert(
            self::TABLE,
            [
                (new \DatabaseRowInput)
                    ->setField("handle", $handle)
            ]
        )) {
            return true;
        } else return false;
    }
}
