import 'dart:async';
import 'package:fluttercity/api/common/ps_resource.dart';
import 'package:fluttercity/api/common/ps_status.dart';
import 'package:fluttercity/db/blog_dao.dart';
import 'package:fluttercity/db/category_map_dao.dart';
import 'package:fluttercity/db/cateogry_dao.dart';
import 'package:fluttercity/db/comment_detail_dao.dart';
import 'package:fluttercity/db/comment_header_dao.dart';
import 'package:fluttercity/db/item_collection_dao.dart';
import 'package:fluttercity/db/item_dao.dart';
import 'package:fluttercity/db/item_map_dao.dart';
import 'package:fluttercity/db/rating_dao.dart';
import 'package:fluttercity/db/sub_category_dao.dart';
import 'package:fluttercity/repository/Common/ps_repository.dart';
import 'package:fluttercity/viewobject/item.dart';

class ClearAllDataRepository extends PsRepository {
  Future<dynamic> clearAllData(
      StreamController<PsResource<List<Item>>> allListStream) async {
    final ItemDao _productDao = ItemDao.instance;
    final CategoryDao _categoryDao = CategoryDao();
    final CommentHeaderDao _commentHeaderDao = CommentHeaderDao.instance;
    final CommentDetailDao _commentDetailDao = CommentDetailDao.instance;
    final CategoryMapDao _categoryMapDao = CategoryMapDao.instance;
    final ItemCollectionDao _productCollectionDao = ItemCollectionDao.instance;
    final ItemMapDao _productMapDao = ItemMapDao.instance;
    final RatingDao _ratingDao = RatingDao.instance;
    final SubCategoryDao _subCategoryDao = SubCategoryDao();
    final BlogDao _blogDao = BlogDao.instance;
    await _productDao.deleteAll();
    await _blogDao.deleteAll();
    await _categoryDao.deleteAll();
    await _commentHeaderDao.deleteAll();
    await _commentDetailDao.deleteAll();
    await _categoryMapDao.deleteAll();
    await _productCollectionDao.deleteAll();
    await _productMapDao.deleteAll();
    await _ratingDao.deleteAll();
    await _subCategoryDao.deleteAll();

    allListStream.sink.add(await _productDao.getAll(status: PsStatus.SUCCESS));
  }
}
