----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 Date: Mon, 18 Dec 2017 15:01:20 +0000
 Error: 2006 - MySQL server has gone away
 IP Address: 37.78.84.49 - /forum/index.php?app=core&module=search&do=search&andor_type=&sid=fc5dea1e022e7b48fc40d6717bf0e0a6&search_app_filters[forums][sortKey]=date&search_tags=недорого&search_app_filters[forums][sortKey]=date&search_app_filters[forums][searchInKey]=&search_term=&search_app=forums&search_app_filters[forums][searchInKey]=&search_app_filters[forums][sortKey]=posts&search_app_filters[forums][sortDir]=
 ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 mySQL query error: SELECT * FROM skin_cache WHERE cache_set_id=5 AND cache_value_1='skin_forums_global'
 .--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------.
 | File                                                                       | Function                                                                      | Line No.          |
 |----------------------------------------------------------------------------+-------------------------------------------------------------------------------+-------------------|
 | admin/sources/classes/output/publicOutput.php                              | [output].loadTemplate                                                         | 1508              |
 '----------------------------------------------------------------------------+-------------------------------------------------------------------------------+-------------------'
 | cache/skin_cache/cacheid_5/skin_global.php                                 | [output].getTemplate                                                          | 1492              |
 '----------------------------------------------------------------------------+-------------------------------------------------------------------------------+-------------------'
 | cache/skin_cache/cacheid_5/skin_global.php                                 | [skin_global_5].quickSearch                                                   | 339               |
 '----------------------------------------------------------------------------+-------------------------------------------------------------------------------+-------------------'
 | admin/sources/classes/output/formats/html/htmlOutput.php                   | [skin_global_5].globalTemplate                                                | 312               |
 '----------------------------------------------------------------------------+-------------------------------------------------------------------------------+-------------------'
 | admin/sources/classes/output/publicOutput.php                              | [htmlOutput].fetchOutput                                                      | 2971              |
 '----------------------------------------------------------------------------+-------------------------------------------------------------------------------+-------------------'
 | admin/applications/core/modules_public/search/search.php                   | [output].sendOutput                                                           | 208               |
 '----------------------------------------------------------------------------+-------------------------------------------------------------------------------+-------------------'
 | admin/sources/base/ipsController.php                                       | [public_core_search_search].doExecute                                         | 306               |
 '----------------------------------------------------------------------------+-------------------------------------------------------------------------------+-------------------'