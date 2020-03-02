<?php


namespace ChameleonSystem\GraphQLBundle\Resolver;


final class Users
{


    public static function getAll()
    {
        $userList = \TdbDataExtranetUserList::GetList();
        $users = [];

        while (false !== ($user = $userList->Next())) {
            $users[] = self::userToSchema($user);
        }

        return $users;
    }

    public static function userToSchema(\TdbDataExtranetUser $user)
    {
        return [
            'id' => $user->id,
            'firstName' => $user->fieldFirstname,
            'lastName' => $user->fieldLastname,
            'email' => $user->fieldEmail,
        ];
    }

    public static function getById(string $id)
    {
        $user = \TdbDataExtranetUser::GetNewInstance();
        $user->Load($id);

        return self::userToSchema($user);
    }
}
