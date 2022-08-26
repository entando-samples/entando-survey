<#assign wp=JspTaglibs["/aps-core"]>
<html>
<head>
   <@wp.fragment code="es_keycloak_auth" escapeXml=false />
   <title><@wp.currentPage param="title" /></title>
</head>
<body>
<@wp.show frame=0 />
</body>
</html>
