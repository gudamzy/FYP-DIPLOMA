<?php
/*
 * Photos for the menu cards.
 *
 * 1) Your own photo always wins: put it in  image/menu/  named after the menu item
 *      SET A               -> image/menu/set-a.jpg
 *      NASI GORENG PATTAYA -> image/menu/nasi-goreng-pattaya.jpg
 *    (lower case, spaces become "-"; .jpg, .jpeg, .png and .webp all work)
 *
 * 2) Otherwise the free Unsplash photo listed in menu_photo_ids() below is used.
 *
 * 3) If neither exists (e.g. a new menu item), a simple icon is shown instead.
 */

# Free photos from Unsplash (Unsplash License). Key = menu name in lower case with "-".
# Photo pages: https://unsplash.com/photos/<page id>
function menu_photo_ids()
{
    return [
        'set-a'               => '1732185269471-b62b52ca46f9', # RhanTswYckA  ayam bakar with rice
        'set-b'               => '1727404583890-0eaef7d5b800', # cDi0o1QoUos  chicken with lettuce & tomato (penyet style)
        'set-c'               => '1765265432611-17d3f2da2d5d', # 6fmwFzYbLqk  seasoned fried fish with lime
        'set-d'               => '1630315500315-43112e2bfd88', # dPoNxtkaz0Q  chicken in creamy sauce
        'set-e'               => '1645696301019-35adcc18fc21', # LuBREV-wIik  chicken & vegetables
        'set-rahmah'          => '1622137157429-a33cf0d997b9', # eWZJlxEIRN8  omelette
        'nasi-goreng-ayam'    => '1741231953125-96d4ab264d96', # -TqVLmsTxZM  nasi goreng
        'nasi-goreng-pattaya' => '1564671165093-20688ff1fffa', # ykThMylLsbY  rice with egg
        'nasi-goreng-tomyam'  => '1680674774705-90b4904b3a7f', # o6Oq7rBMqVc  fried rice with prawns
        'nasi-goreng-kampung' => '1610653596329-eacc50074ad2', # gAWCYaJMYk8  nasi goreng with cucumber
        'kopi-ais'            => '1578314675249-a6910f80cc4e', # HRfnyL6AO-A  iced coffee in plastic cup
        'milo-ais'            => '1621221814951-fa755dd0c993', # SnbEXxIj6wk  iced chocolate drink
        'nescafe-ais'         => '1643250096514-736909e95a80', # y2Dfk2Kt89o  iced coffee with milk
        'limau-ais'           => '1507281549113-040fcfef650e', # p5EiqkBYIEE  lime juice
        'tea-o-ais'           => '1599767431130-41b1c51d9a7b', # mAYaGExwdEo  iced tea
        'teh-tarik'           => '1788016284567-f612a7383cb2', # O8hI57L4C_w  iced milk tea
        'jus-mangga'          => '1623065422902-30a2d299bbe4', # KlVIYmGVRQ8  mango smoothie
        'jus-tembikai'        => '1683166263544-e754e85c3e7c', # aKzkiewJ8ng  watermelon juice
    ];
}

function menu_image_slug($name)
{
    $slug = strtolower(trim($name));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    return trim($slug, '-');
}

function menu_image($name)
{
    $slug = menu_image_slug($name);
    if ($slug === '') {
        return null;
    }

    # 1) Own photo in image/menu/
    $root = dirname(__DIR__); # project folder (one level above "includes")
    foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
        $file = 'image/menu/' . $slug . '.' . $ext;
        if (is_file($root . '/' . $file)) {
            return $file;
        }
    }

    # 2) Unsplash photo
    $ids = menu_photo_ids();
    if (isset($ids[$slug])) {
        return 'https://images.unsplash.com/photo-' . $ids[$slug]
             . '?auto=format&fit=crop&w=600&h=400&q=70';
    }

    # 3) No photo
    return null;
}

# Icon shown when a menu item has no photo
function menu_placeholder_icon($category)
{
    $icons = [
        'Set Meals'   => '🍛',
        'Nasi Goreng' => '🍳',
        'Drinks'      => '🥤',
    ];
    return $icons[$category] ?? '🍽️';
}