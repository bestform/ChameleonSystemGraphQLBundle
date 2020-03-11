<?php


namespace ChameleonSystem\GraphQLBundle\Resolver;


final class Orders
{


    public static function ordersForUser(string $userId)
    {
        $user = \TdbDataExtranetUser::GetNewInstance();
        $user->Load($userId);

        $orders = [];
        $orderList = $user->GetFieldShopOrderList();

        while (false !== ($order = $orderList->Next())) {
            $orders[] = self::orderToSchema($order);
        }

        return $orders;
    }

    private static function orderToSchema(\TdbShopOrder $order)
    {
        return [
            'id' => $order->id,
            'productCount' => $order->fieldCountArticlesFormated,
            'value' => $order->fieldValueTotalFormated,
        ];
    }

    public static function articlesForOrder(string $orderId)
    {
        $order = \TdbShopOrder::GetNewInstance();
        $order->Load($orderId);

        $articleList = $order->GetFieldShopOrderItemList();
        $articles = [];

        while (false !== ($article = $articleList->Next())){
            $articleSchema = Products::articleToSchema($article->GetFieldShopArticle());
            $articleSchema['amount'] = $article->fieldOrderAmountFormated;
            $articles[] = $articleSchema;
        }

        return $articles;
    }

    public static function getUserForOrder(string $orderId) {
        $order = \TdbShopOrder::GetNewInstance();
        $order->Load($orderId);

        $user = $order->GetFieldDataExtranetUser();

        if (!$user) return null;

        return Users::userToSchema($user);
    }

    public static function getAll()
    {
        $orderList = \TdbShopOrderList::GetList();

        $orders = [];
        while (false !== ($order = $orderList->Next())) {
            $orders[] = self::orderToSchema($order);
        }

        return $orders;
    }
}
