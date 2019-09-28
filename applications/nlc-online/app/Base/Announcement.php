<?php

namespace NLC\Base;

use NLC\Throwable\AccessDenied;
use NLC\Throwable\AnnouncementNotFound;
use PuzzleUser;

class Announcement implements \JsonSerializable
{
    private static $singleton = [];

    private $id;
    private $title;
    private $content;
    private $is_read;

    #region Static
    /**
     * Buat pengumuman. Ntar jadi 1 item pengumuman dengan status belum dibaca oleh semua user
     *
     * @return void
     */
    public static function create(string $title, string $content)
    {
        if (PuzzleUser::isAccess(USER_AUTH_EMPLOYEE)) {
            \Database::insert(
                "app_nlc_announcement",
                [
                    (new \DatabaseRowInput)
                        ->setField("title", $title)
                        ->setField("content", $content)
                ]
            );
            return self::load(\Database::lastId());
        } else throw new AccessDenied;
    }

    public static function load(int $id)
    {
        return self::$singleton[$id] ?? self::$singleton[$id] = new self($id);
    }

    /**
     * List semuanya, termasuk unread
     *
     * @return void
     */
    public static function list()
    {
        $a = [];
        $db = \Database::execute("SELECT id FROM app_nlc_announcement order by id desc");
        while ($row = $db->fetch_assoc()) {
            $a[] = self::load($row['id']);
        }
        return $a;
    }

    public static function hasUnread(): bool
    {
        $db = \Database::execute("SELECT 1 FROM app_nlc_announcement a LEFT JOIN app_nlc_announcement_read r ON a.id = r.announcement_id AND `user_id` = '?' WHERE r.announcement_id is null", PuzzleUser::active()->id);
        return (bool) ($db->num_rows > 0);
    }
    #endregion

    #region Object
    private function __construct(int $id)
    {
        $db = \Database::getRow("app_nlc_announcement", "id", $id);
        if ($db) {
            $this->id = $id;
            $this->title = $db['title'];
            $this->content = $db['content'];
            $st = \Database::execute("SELECT 1 FROM app_nlc_announcement_read WHERE announcement_id = '?' and `user_id` = '?'", $this->id, PuzzleUser::active()->id);
            $this->is_read = (bool) ($st->num_rows > 0);
        } else throw new AnnouncementNotFound;
    }

    /**
     * Tandai notifikasi dibaca oleh SATU user
     *
     * @return bool
     */
    public function markAsRead(): bool
    {
        if (\Database::insert("app_nlc_announcement_read", [
            (new \DatabaseRowInput)
                ->setField("announcement_id", $this->id)
                ->setField("user_id", PuzzleUser::active()->id)
        ])) {
            $this->is_read = true;
            return true;
        }
        return false;
    }

    public function jsonSerialize()
    {
        return [
            "id" => (int) $this->id,
            "title" => $this->title,
            "content" => $this->content,
            "is_read" => (bool) $this->is_read,
        ];
    }
    #endregion
}
