<?php
namespace Forum\DTO;

use Avatar;
use Context;
use User;

class Member
{
    public function __construct(
        public string $id,
        public string $username,
        public string $name,
        public string $avatar_url,
        public string $role
    ) {}

    public static function fromArray(array $data = []): self
    {
        return new self(
            $data['id'] ?? '',
            $data['username'] ?? '',
            $data['name'] ?? '',
            $data['avatar_url'] ?? '',
            $data['role'] ?? ''
        );
    }

    public static function fromUser(User $user, $course_id = null): self
    {
        $course_id = $course_id ?? Context::getId();
        $role = $GLOBALS['perm']->get_studip_perm($course_id, $user->user_id);

        return self::fromArray([
            'id' => $user->user_id,
            'username' => $user->username,
            'name' => $user->getFullName(),
            'avatar_url' => Avatar::getAvatar($user->user_id)->getURL(Avatar::NORMAL),
            'role' => in_array($role, ['dozent', 'tutor']) ? 'moderator' : 'author'
        ]);
    }

    public function toRawArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'name' => $this->name,
            'avatar_url' => $this->avatar_url,
            'role' => $this->role
        ];
    }
}
