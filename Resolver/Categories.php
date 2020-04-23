<?php


namespace ChameleonSystem\GraphQLBundle\Resolver;


final class Categories
{

    public static function categoryToSchema($category) {
        return [
            'id' => $category->id,
            'name' => $category->fieldName,
            'path' => $category->fieldUrlPath
        ];
    }

    public static function allCategories() {
        $categoryList = \TdbShopCategoryList::GetList();

        $cats = [];
        while (false !== ($cat = $categoryList->Next())){
            $cats[] = self::categoryToSchema($cat);
        }

        return $cats;
    }

    public static function categoriesForArticle($articleId)
    {
        $article = \TdbShopArticle::GetNewInstance();
        $article->Load($articleId);

        $catList = $article->GetFieldShopCategoryList();

        $cats = [];

        while (false !== ($cat = $catList->Next())) {
            $cats[] = self::categoryToSchema($cat);
        }

        return $cats;
    }
}
