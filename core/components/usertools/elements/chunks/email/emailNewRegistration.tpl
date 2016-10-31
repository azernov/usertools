<h1>На сайте зарегистрировался новый пользователь</h1>
<p>Ниже представлена контактная информация:</p>
<ul>
    [[+type:is=`fl`:then=`<li>Тип: физическое лицо</li>`:else=``]]
    [[+type:is=`ul`:then=`<li>Тип: юрлицо</li>`:else=``]]
    [[+firstname:notempty=`<li>имя: [[+firstname]]</li>`]]
    [[+lastname:notempty=`<li>фамилия: [[+lastname]]</li>`]]
    [[+company:notempty=`<li>компания: [[+company]]</li>`]]
    [[+inn:notempty=`<li>ИНН: [[+inn]]</li>`]]
    [[+kpp:notempty=`<li>КПП: [[+kpp]]</li>`]]
    [[+director_fullname:notempty=`<li>имя директора: [[+director_fullname]]</li>`]]
    [[+address:notempty=`<li>адрес: [[+address]]</li>`]]
    [[+website:notempty=`<li>веб-сайт: [[+website]]</li>`]]
    [[+phone:notempty=`<li>телефон: [[+phone]]</li>`]]
    [[+email:notempty=`<li>e-mail: [[+email]]</li>`]]
</ul>

<p>
    Для того, чтобы активировать пользователя, откройте административную панель сайта, перейдите в интерфейс "UserTools/Дилеры" и измените поле "Активный".
    Или перейдите по <a href="[[++site_url]]manager/?a=user/update&id=[[+id]]&namespace=usertools">ссылке</a>
</p>