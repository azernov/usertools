<?

define("COMPONENT_BUILD", true);//Поставить в true, если требуется сборка компонента и false, если необходимо обновить только модели

$package_table_prefix = 'ut_';
$package_name = 'usertools';
$package_class_prefix = 'ut';

$regenerate_schema = true;

//Поставить в false если не нужно перезаписывать файлы классов
$regenerate_classes = true;

//Поставить false, если не нужно перезаписывать map.inc файлы
$regenerate_maps = true;

$root = dirname(dirname(dirname(dirname(__FILE__)))).'/';

if(COMPONENT_BUILD)
{
    $sources = array (
        "root" => $root,
        "build" => $root ."_development/$package_name/_build/",
        "resolvers" => $root . "_development/$package_name/_build/resolvers/",
        "data" => $root . "_development/$package_name/_build/data/",
        "source_core" => $root."core/components/$package_name/",
        "lexicon" => $root . "core/components/$package_name/lexicon/",
        "source_assets" => $root."assets/components/$package_name/",
        "docs" => $root."core/components/$package_name/docs/",

        "package_dir" => $root."_development/$package_name/_core/components/$package_name",
        "model_dir" => $root."_development/$package_name/_core/components/$package_name/model",
        "class_dir" => $root. "_development/$package_name/_core/components/$package_name/model/$package_name",
        "schema_dir" => $root. "_development/$package_name/_core/components/$package_name/model/schema",
        "mysql_class_dir" => $root. "_development/$package_name/_core/components/$package_name/model/$package_name/mysql",

        //Это основной файл, который мы правим ручками
        "xml_schema_file" => $root. "_development/$package_name/_core/components/$package_name/model/schema/$package_name.mysql.schema.xml",

        //Это новый, сгенерированный файл, из которого мы будем забирать изменения и вставлять их в основной файл
        "new_xml_schema_file" => $root. "_development/$package_name/_core/components/$package_name/model/schema/$package_name.mysql.schema.new.xml"
    );
}
else
{
    $sources = array (
        "root" => $root,
        "build" => $root ."_development/$package_name/_build/",
        "resolvers" => $root . "_development/$package_name/_build/resolvers/",
        "data" => $root . "_development/$package_name/_build/data/",
        "source_core" => $root."core/components/$package_name/",
        "lexicon" => $root . "core/components/$package_name/lexicon/",
        "source_assets" => $root."assets/components/$package_name/",
        "docs" => $root."core/components/$package_name/docs/",

        "package_dir" => $root."core/components/$package_name",
        "model_dir" => $root."core/components/$package_name/model",
        "class_dir" => $root. "core/components/$package_name/model/$package_name",
        "schema_dir" => $root. "core/components/$package_name/model/schema",
        "mysql_class_dir" => $root. "core/components/$package_name/model/$package_name/mysql",

        //Это основной файл, который мы правим ручками
        "xml_schema_file" => $root. "core/components/$package_name/model/schema/$package_name.mysql.schema.xml",

        //Это новый, сгенерированный файл, из которого мы будем забирать изменения и вставлять их в основной файл
        "new_xml_schema_file" => $root. "core/components/$package_name/model/schema/$package_name.mysql.schema.new.xml"
    );
}

unset($root);

//Объявляем базовые константы
define("MODX_CORE_PATH",$sources['root'].'core/');
define("MODX_BASE_PATH",$sources['root']);
define('MODX_BASE_URL', '/');