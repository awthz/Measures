<?php

namespace Classes\Models;

use Classes\Models\User;
use Classes\Models\Assessor;
use Classes\Models\AlternativeAccount;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 * @package Classes\Models
 * @property int $id
 * @method static User|null find(int $ID)
 */
class User extends Model
{
    protected $table = 'wp_users';

    protected $primaryKey = 'ID';

    protected $guarded = ['user_pass'];

    public function getId(): int
    {
        return $this->ID;
    }

    public function getName(): string
    {
        return $this->display_name;
    }

    public function assessor()
    {
        return $this->belongsTo(Assessor::class, 'ID', 'user_id');
    }

    public function getAssessor()
    {
        return $this->assessor()->first();
    }

    public static function current(): ?User
    {
        $user_id = get_current_user_id();

        if ($user_id > 0) {
            return once(function () use ($user_id) {
                return User::find($user_id);
            });
        }

        return null;
    }

    /**
     * Get all of the accounts which you can log into.
     *
     * @return \Illuminate\Database\Eloquent\Collection|User[] The users collection.
     */
    public static function getAccounts()
    {
        $user = User::current();

        // If this is an alternative account then get the base account first.
        $base = AlternativeAccount::where('account_id', $user->getKey())->first(['user_id']);

        // Get all of the accounts.
        $accounts = AlternativeAccount::where('user_id', $base ? $base->user_id : $user->getKey())
            ->get(['user_id', 'account_id']);

        if ($accounts->count()) {
            // Get all of the users which you can log into.
            $accounts = User::whereIn($user->getKeyName(), $accounts->pluck('account_id'))
                ->orWhereIn($user->getKeyName(), $accounts->pluck('user_id')->unique())
                ->get()
                ->filter(function ($account) use ($user) {
                    // Remove the account if it is your own account which you are currently logged into.
                    return $account->getKey() !== $user->getKey();
                });
        }

        return $accounts->values();
    }

    /**
     * Switch to the given user if they are allowed to.
     *
     * @param int $user_id The users ID to switch to.
     */
    public function switchTo(int $user_id)
    {
        // Make sure they are allowed to switch to this account.
        $accounts = User::getAccounts();

        if ($accounts->where((new User)->getKeyName(), $user_id)->count() === 1) {
            // Get the account to switch to.
            $account = $accounts->where((new User)->getKeyName(), $user_id)->first();

            // Switch to the account.
            wp_clear_auth_cookie();
            wp_set_current_user($account->getKey());
            wp_set_auth_cookie($account->getKey());
            wp_safe_redirect(user_admin_url());
            die;
        } else {
            http_response_code(403);
            die(json_encode(['code' => 403, 'message' => 'Permission denied.']));
        }
    }
}
