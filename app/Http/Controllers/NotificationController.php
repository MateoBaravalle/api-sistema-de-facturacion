<?php

namespace App\Http\Controllers;

use App\Http\Requests\NotificationRequest\StoreNotificationRequest;
use App\Http\Requests\NotificationRequest\UpdateNotificationRequest;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function getAll(Request $request): JsonResponse
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 10);
            $notifications = $this->notificationService->getAllNotifications($page, $perPage);
            return $this->successResponse('Notificaciones recuperadas', ['notificaciones' => $notifications]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(StoreNotificationRequest $request): JsonResponse
    {
        try {
            $notification = $this->notificationService->createNotification($request->validated());
            return $this->successResponse('Notificación creada', ['notificación' => $notification], 201);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(UpdateNotificationRequest $request, int $id): JsonResponse
    {
        try {
            $notification = $this->notificationService->updateNotification($id, $request->validated());
            return $this->successResponse('Notificación actualizada', ['notificación' => $notification]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->notificationService->deleteNotification($id);
            return $this->successResponse('Notificación eliminada');
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
