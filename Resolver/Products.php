<?php


namespace ChameleonSystem\GraphQLBundle\Resolver;


use TCMSImage;

final class Products
{

    public static function articleToSchema(\TdbShopArticle $article)
    {
        return [
            'id' => $article->id,
            'title' => $article->fieldName,
            'price' => $article->fieldPriceFormated,
            'short_description' => $article->fieldDescriptionShort,
            'description' => $article->fieldDescription,
            'url' => $article->getLink(true)
        ];
    }

    public static function thumbForArticle($articleId, $width, $height)
    {
        $article = \TdbShopArticle::GetNewInstance();
        $article->Load($articleId);

        $tdbimage = $article->GetPrimaryImage();
        if (!$tdbimage) return null;

        $image = $tdbimage->GetImage(0, 'cms_media_id');
        if (!$image) return null;

        $thumb = $image->GetThumbnail($width, $height);
        return [
            'url' => $thumb->GetFullURL(),
            'id' => $thumb->id,
        ];
    }

    public static function imageForArticle($articleId)
    {
        $article = \TdbShopArticle::GetNewInstance();
        $article->Load($articleId);

        $tdbimage = $article->GetPrimaryImage();
        if (!$tdbimage) return null;

        $image = $tdbimage->GetImage(0, 'cms_media_id');
        if (!$image) return null;

        return [
            'url' => $image->GetFullURL(),
            'id' => $image->id,
        ];

    }

    public static function getAll(int $first, int $offset, string $match = null)
    {
        $list = \TdbShopArticleList::GetList();
        $list->SetPagingInfo($offset, $first);
        if ($match) {
            $list->AddFilterString('name LIKE \'%'. \MySqlLegacySupport::getInstance()->real_escape_string($match) . '%\'');
        }
        $all = [];

        while (false !== ($a = $list->Next())) {
            $all[] = self::articleToSchema($a);
        }


        return $all;
    }

    public static function categoriesForArticle($articleId)
    {
        $article = \TdbShopArticle::GetNewInstance();
        $article->Load($articleId);

        $catList = $article->GetFieldShopCategoryList();

        $cats = [];

        while (false !== ($cat = $catList->Next())) {
            $cats[] = [
                'id' => $cat->id,
                'name' => $cat->fieldName,
                'path' => $cat->fieldUrlPath
            ];
        }

        return $cats;
    }

    public static function getAllCheaperThan($cheaperThan, int $first, int $offset, string $match = null)
    {
        $list = \TdbShopArticleList::GetList();
        $list->SetPagingInfo($offset, $first);
        $list->AddFilterString('price < ' . $cheaperThan);
        if ($match) {
            $list->AddFilterString('name LIKE \'%'. \MySqlLegacySupport::getInstance()->real_escape_string($match) . '%\'');
        }

        $all = [];

        while (false !== ($a = $list->Next())) {
            $all[] = self::articleToSchema($a);
        }


        return $all;
    }


}
