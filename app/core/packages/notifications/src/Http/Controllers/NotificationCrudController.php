<?php

namespace Bo\Notifications\Http\Controllers;

use Alert;
use App\Models\User;
use Bo\Base\Http\Controllers\CrudController;
use Bo\Base\Http\Controllers\Operations\ListOperation;
use Bo\Base\Http\Controllers\Operations\ShowOperation;
    use Bo\Notifications\Models\Notification;
use Carbon\Carbon;
use Exception;
use Request;

class NotificationCrudController extends CrudController
{
    use ListOperation;
    use ShowOperation;

    public function setup()
    {
        $this->crud->setModel('Bo\Notifications\Models\Notification');
        $this->crud->setRoute(config('bo.base.route_prefix') . '/notification');
        $this->crud->setEntityNameStrings('notification', 'notifications');

        $this->crud->addClause('orderBy', 'created_at', 'desc');

        $this->crud->addClause('where', 'notifiable_id', bo_user()->id);
        $this->crud->addClause('where', 'notifiable_type', config('bo.base.user_model_fqn'));

        if (!Request::get('show_dismissed')) {
            $this->crud->addClause('whereNull', 'read_at');
        }

        $this->crud->addButtonFromModelFunction('top', 'dismiss_all', 'dismissAllButton', 'beginning');

        $this->crud->addButtonFromModelFunction('line', 'action', 'actionButton', 'end');
        $this->crud->addButtonFromModelFunction('line', 'dismiss', 'dismissButton', 'end');

        $this->crud->denyAccess(['create', 'delete', 'update', 'show']);
    }

    public function dismissAll()
    {
        bo_user()->unreadNotifications->markAsRead();

        Alert::success('All notifications dismissed')->flash();

        return redirect()->back();
    }

    public function dismiss($notificationId)
    {
        $notification = Notification::findOrFail($notificationId);

        $notification->read_at = Carbon::now();
        $notification->save();

        Alert::success('Notification dismissed')->flash();

        return redirect()->back();
    }

    public function unreadCount()
    {
        $count = bo_user()->unreadNotifications->count();

        $lastNotification = bo_user()->unreadNotifications()->orderBy('created_at', 'desc')->first();

        return response()->json([
            'count'             => $count,
            'last_notification' => $lastNotification ? $lastNotification->data : null,
        ]);
    }

    protected function setupShowOperation()
    {
        $this->crud->set('show.setFromDb', false);

        $this->setupListOperation();
    }

    protected function setupListOperation()
    {
        $this->crud->setActionsColumnPriority(-1);
        // $this->crud->disableResponsiveTable();

        // Filters

        $this->crud->addFilter(
            [
                'type'  => 'simple',
                'name'  => 'show_dismissed',
                'label' => 'Show Dismissed',
            ],
            false,
            function () {
                $this->crud->addClause('whereNotNull', 'read_at');
            }
        );

        // columns

        $this->crud->addColumn([
            'label' => 'Date',
            'type'  => 'datetime',
            'name'  => 'created_at',
        ]);

        $this->crud->addColumn([
            'name'     => 'message',
            'label'    => 'Message',
            'type'     => 'custom_html',
            'priority' => -1,
            'value' => function ($entry) {
                return '<div style="display:inline-block; max-width:100%; white-space: pre-wrap;">' .
                    ($entry->data->message_long ?? $entry->data->message ?? '-') .
                    '</div>';
            },
        ]);
    }
}
