import 'dart:async';
import 'package:fluttercity/api/common/ps_resource.dart';
import 'package:fluttercity/api/common/ps_status.dart';
import 'package:fluttercity/api/ps_api_service.dart';
import 'package:fluttercity/constant/ps_constants.dart';
import 'package:fluttercity/db/comment_header_dao.dart';
import 'package:fluttercity/viewobject/comment_header.dart';
import 'package:sembast/sembast.dart';

import 'Common/ps_repository.dart';

class CommentHeaderRepository extends PsRepository {
  CommentHeaderRepository(
      {required PsApiService psApiService,
      required CommentHeaderDao commentHeaderDao}) {
    _psApiService = psApiService;
    _commentHeaderDao = commentHeaderDao;
  }

  String primaryKey = 'id';
 late PsApiService _psApiService;
 late CommentHeaderDao _commentHeaderDao;

  Future<dynamic> insert(CommentHeader commentHeader) async {
    return _commentHeaderDao.insert(primaryKey, commentHeader);
  }

  Future<dynamic> update(CommentHeader commentHeader) async {
    return _commentHeaderDao.update(commentHeader);
  }

  Future<dynamic> delete(CommentHeader commentHeader) async {
    return _commentHeaderDao.delete(commentHeader);
  }

  Future<dynamic> syncCommentByIdAndLoadCommentList(
      String commentId,
      String itemId,
      StreamController<PsResource<List<CommentHeader>>> commentHeaderListStream,
      bool isConnectedToInternet,
      int limit,
      int offset,
      PsStatus status,
      {bool isNeedDelete = true,
      bool isLoadFromServer = true}) async {
    final Finder commentFinder = Finder(filter: Filter.equals('id', commentId));
    final Finder finder = Finder(filter: Filter.equals('item_id', itemId));
    commentHeaderListStream.sink
        .add(await _commentHeaderDao.getAll(finder: finder, status: status));

    if (isConnectedToInternet) {
      final PsResource<CommentHeader> _resource =
          await _psApiService.getCommentHeaderById(commentId);

      if (_resource.status == PsStatus.SUCCESS) {
        await _commentHeaderDao.deleteWithFinder(finder);
        await _commentHeaderDao.update(_resource.data!, finder: commentFinder);
      } else {
        if (_resource.errorCode == PsConst.ERROR_CODE_10001) {
          await _commentHeaderDao.deleteWithFinder(finder);
        }
      }
      commentHeaderListStream.sink
          .add(await _commentHeaderDao.getAll(finder: finder));
    }
  }

  Future<dynamic> getAllCommentList(
      String itemId,
      StreamController<PsResource<List<CommentHeader>>> commentHeaderListStream,
      bool isConnectedToInternet,
      int limit,
      int offset,
      PsStatus status,
      {bool isNeedDelete = true,
      bool isLoadFromServer = true}) async {
    final Finder finder = Finder(filter: Filter.equals('item_id', itemId));
    commentHeaderListStream.sink
        .add(await _commentHeaderDao.getAll(finder: finder, status: status));

    if (isConnectedToInternet) {
      final PsResource<List<CommentHeader>> _resource =
          await _psApiService.getCommentList(itemId, limit, offset);

      if (_resource.status == PsStatus.SUCCESS) {
        if (isNeedDelete) {
          await _commentHeaderDao.deleteWithFinder(finder);
        }
        await _commentHeaderDao.insertAll(primaryKey, _resource.data!);
        commentHeaderListStream.sink
            .add(await _commentHeaderDao.getAll(finder: finder));
      } else {
        if (_resource.errorCode == PsConst.ERROR_CODE_10001) {
          if (isNeedDelete) {
            await _commentHeaderDao.deleteWithFinder(finder);
          }
        }
      }
      commentHeaderListStream.sink
          .add(await _commentHeaderDao.getAll(finder: finder));
    }
  }

  Future<dynamic> getNextPageCommentList(
      String itemId,
      StreamController<PsResource<List<CommentHeader>>> commentHeaderListStream,
      bool isConnectedToInternet,
      int limit,
      int offset,
      PsStatus status,
      {bool isLoadFromServer = true}) async {
    final Finder finder = Finder(filter: Filter.equals('item_id', itemId));
    commentHeaderListStream.sink
        .add(await _commentHeaderDao.getAll(finder: finder, status: status));

    if (isConnectedToInternet) {
      final PsResource<List<CommentHeader>> _resource =
          await _psApiService.getCommentList(itemId, limit, offset);

      if (_resource.status == PsStatus.SUCCESS) {
        await _commentHeaderDao.insertAll(primaryKey, _resource.data!);
      }
      commentHeaderListStream.sink
          .add(await _commentHeaderDao.getAll(finder: finder));
    }
  }

  Future<PsResource<List<CommentHeader>>> postCommentHeader(
      StreamController<PsResource<List<CommentHeader>>> commentHeaderListStream,
      Map<dynamic, dynamic> jsonMap,
      bool isConnectedToInternet,
      PsStatus status,
      {bool isLoadFromServer = true}) async {
    final PsResource<List<CommentHeader>> _resource =
        await _psApiService.postCommentHeader(jsonMap);
    if (_resource.status == PsStatus.SUCCESS) {
      return _resource;
    } else {
      final Completer<PsResource<List<CommentHeader>>> completer =
          Completer<PsResource<List<CommentHeader>>>();
      completer.complete(_resource);
      return completer.future;
    }
  }
}
