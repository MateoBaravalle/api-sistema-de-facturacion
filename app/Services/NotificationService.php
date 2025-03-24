<?php

namespace App\Services;

use App\Models\Notification;
use Illuminate\Pagination\LengthAwarePaginator;

class NotificationService extends Service
{
    protected const MODEL = 'notification';

    public function __construct(Notification $notification)
    {
        parent::__construct($notification, self::MODEL);
    }

    public function getAllNotifications(array $params): LengthAwarePaginator
    {
        $query = $this->model->query()->where('user_id', auth()->user()->id);
        $query = $this->getFilteredAndSorted(
            $query,
            $params
        );

        return $this->getAll($params['page'], $params['per_page'], $query);
    }

    public function getNotificationById(int $id): Notification
    {
        return $this->getById($id);
    }

    public function createNotification(array $data): Notification
    {
        return $this->create($data);
    }

    public function updateNotification(int $id, array $data): Notification
    {
        return $this->update($id, $data);
    }

    public function deleteNotification(int $id): bool
    {
        return $this->delete($id);
    }
}
