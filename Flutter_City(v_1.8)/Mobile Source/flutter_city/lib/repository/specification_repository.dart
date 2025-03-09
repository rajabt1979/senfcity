import 'dart:async';
import 'package:fluttercity/api/common/ps_resource.dart';
import 'package:fluttercity/api/common/ps_status.dart';
import 'package:fluttercity/api/ps_api_service.dart';
import 'package:fluttercity/constant/ps_constants.dart';
import 'package:fluttercity/db/add_specification_dao.dart';
import 'package:fluttercity/db/specification_dao.dart';
import 'package:fluttercity/viewobject/add_specification.dart';
import 'package:fluttercity/viewobject/api_status.dart';
import 'package:fluttercity/viewobject/item_spec.dart';

import 'Common/ps_repository.dart';

class SpecificationRepository extends PsRepository {
  SpecificationRepository(
      {required PsApiService psApiService,
      required SpecificationDao specificationDao}) {
    _psApiService = psApiService;
    _specificationDao = specificationDao;
  }

  String primaryKey = 'id';
 late PsApiService _psApiService;
 late SpecificationDao _specificationDao;

  Future<dynamic> insert(ItemSpecification specification) async {
    return _specificationDao.insert(primaryKey, specification);
  }

  Future<dynamic> update(ItemSpecification specification) async {
    return _specificationDao.update(specification);
  }

  Future<dynamic> delete(ItemSpecification specification) async {
    return _specificationDao.delete(specification);
  }

  void sinkSpecificationListStream(
      StreamController<PsResource<List<ItemSpecification>>>?
          specificationListStream,
      PsResource<List<ItemSpecification>>? dataList) {
    if (dataList != null && specificationListStream != null) {
      specificationListStream.sink.add(dataList);
    }
  }

  Future<dynamic> getAllSpecificationList(
      StreamController<PsResource<List<ItemSpecification>>>
          specificationListStream,
      String itemId,
      bool isConnectedToInternet,
      PsStatus status,
      {bool isLoadFromServer = true}) async {
    // Prepare Holder and Map Dao
    // final String paramKey = holder.getParamKey();
    final AddSpecificationDao addSpecificationDao =
        AddSpecificationDao.instance;

    // Load from Db and Send to UI
    sinkSpecificationListStream(
        specificationListStream,
        await _specificationDao.getAllByJoin(
            primaryKey, addSpecificationDao, AddSpecification(),
            status: status));

    // Server Call
    if (isConnectedToInternet) {
      final PsResource<List<ItemSpecification>> _resource =
          await _psApiService.getSpecificationList(itemId);

      if (_resource.status == PsStatus.SUCCESS) {
        // Create Map List
        final List<AddSpecification> specificationItemMapList =
            <AddSpecification>[];
        int i = 0;
        for (ItemSpecification data in _resource.data!) {
          specificationItemMapList.add(AddSpecification(
            id: data.id,
            sorting: i++,
          ));
        }

        // Delete and Insert Map Dao
        await addSpecificationDao.deleteAll();
        await addSpecificationDao.insertAll(
            primaryKey, specificationItemMapList);

        // Insert Item
        await _specificationDao.insertAll(primaryKey, _resource.data!);
      }
      // Delete and Insert Map Dao
      else {
        if (_resource.errorCode == PsConst.ERROR_CODE_10001) {
          await addSpecificationDao.deleteAll();
        }
      }

      // Load updated Data from Db and Send to UI
      sinkSpecificationListStream(
          specificationListStream,
          await _specificationDao.getAllByJoin(
              primaryKey, addSpecificationDao, AddSpecification()));
    }
  }

  // Future<dynamic> getAllSpecificationList(
  //     StreamController<PsResource<List<ItemSpecification>>>
  //         specificationListStream,
  //     bool isConnectedToInternet,
  //     String itemId,
  //     int limit,
  //     int offset,
  //     PsStatus status,
  //     {bool isNeedDelete = true,
  //     bool isLoadFromServer = true}) async {
  //   specificationListStream.sink.add(await _specificationDao.getAll(
  //       finder: Finder(filter: Filter.equals('item_id', itemId)),
  //       status: status));

  //   if (isConnectedToInternet) {
  //     final PsResource<List<ItemSpecification>> _resource =
  //         await _psApiService.getSpecificationList(itemId, limit, offset);

  //     if (_resource.status == PsStatus.SUCCESS) {
  //       if (isNeedDelete) {
  //         await _specificationDao.deleteWithFinder(
  //             Finder(filter: Filter.equals('item_id', itemId)));
  //         await _specificationDao.insertAll(primaryKey, _resource.data);
  //       } else if (_resource.status == PsStatus.ERROR &&
  //           _resource.message == 'No more records') {
  //         // Delete and Insert Map Dao
  //         await _specificationDao.deleteAll();
  //       }
  //       specificationListStream.sink.add(await _specificationDao.getAll(
  //           finder: Finder(filter: Filter.equals('item_id', itemId))));
  //     }
  //   }
  // }

  Future<PsResource<ItemSpecification>> postSpecification(
      Map<dynamic, dynamic> jsonMap,
      bool isConnectedToInternet,
      PsStatus status,
      {bool isLoadFromServer = true}) async {
    final PsResource<ItemSpecification> _resource =
        await _psApiService.postSpecification(jsonMap);
    if (_resource.status == PsStatus.SUCCESS) {
      return _resource;
    } else {
      final Completer<PsResource<ItemSpecification>> completer =
          Completer<PsResource<ItemSpecification>>();
      completer.complete(_resource);
      return completer.future;
    }
  }

  Future<PsResource<ApiStatus>> postDeleteSpecification(
      Map<dynamic, dynamic> jsonMap,
      bool isConnectedToInternet,
      PsStatus status,
      {bool isLoadFromServer = true}) async {
    final PsResource<ApiStatus> _resource =
        await _psApiService.postDeleteSpecification(jsonMap);
    if (_resource.status == PsStatus.SUCCESS) {
      return _resource;
    } else {
      final Completer<PsResource<ApiStatus>> completer =
          Completer<PsResource<ApiStatus>>();
      completer.complete(_resource);
      return completer.future;
    }
  }
}
