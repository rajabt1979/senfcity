import 'dart:async';

import 'package:flutter/material.dart';
import 'package:fluttercity/api/common/ps_resource.dart';
import 'package:fluttercity/api/common/ps_status.dart';
import 'package:fluttercity/provider/common/ps_provider.dart';
import 'package:fluttercity/repository/specification_repository.dart';
import 'package:fluttercity/utils/utils.dart';
import 'package:fluttercity/viewobject/api_status.dart';
import 'package:fluttercity/viewobject/item_spec.dart';

class SpecificationProvider extends PsProvider {
  SpecificationProvider({@required SpecificationRepository? repo, int limit = 0})
      : super(repo, limit) {
    _repo = repo;

    print('Specification Provider: $hashCode');

    Utils.checkInternetConnectivity().then((bool onValue) {
      isConnectedToInternet = onValue;
    });

    specificationListStream =
        StreamController<PsResource<List<ItemSpecification>>>.broadcast();
    subscription = specificationListStream.stream
        .listen((PsResource<List<ItemSpecification>> resource) {
      updateOffset(resource.data!.length);

      _specificationList = resource;
      // ignore: unnecessary_null_comparison
      if (_specificationList != null) {
        selectedSpecificationList = _specificationList.data!;
      }

      if (resource.status != PsStatus.BLOCK_LOADING &&
          resource.status != PsStatus.PROGRESS_LOADING) {
        isLoading = false;
      }

      if (!isDispose) {
        notifyListeners();
      }
    });
  }

  SpecificationRepository? _repo;

  List<ItemSpecification> selectedSpecificationList = <ItemSpecification>[];

  PsResource<List<ItemSpecification>> _specificationList =
      PsResource<List<ItemSpecification>>(
          PsStatus.NOACTION, '', <ItemSpecification>[]);

  PsResource<ItemSpecification> _specification =
      PsResource<ItemSpecification>(PsStatus.NOACTION, '', null);

  PsResource<ApiStatus> _deleteSpecification =
      PsResource<ApiStatus>(PsStatus.NOACTION, '', null);

  PsResource<List<ItemSpecification>> get specificationList =>
      _specificationList;
 late StreamSubscription<PsResource<List<ItemSpecification>>> subscription;
late  StreamController<PsResource<List<ItemSpecification>>> specificationListStream;
  @override
  void dispose() {
    subscription.cancel();
    isDispose = true;
    print('Specification Provider Dispose: $hashCode');
    super.dispose();
  }

  Future<dynamic> loadSpecificationList(
    String itemId,
  ) async {
    isLoading = true;

    isConnectedToInternet = await Utils.checkInternetConnectivity();
    await _repo!.getAllSpecificationList(specificationListStream, itemId,
        isConnectedToInternet, PsStatus.PROGRESS_LOADING);
  }

  Future<dynamic> postSpecification(
    Map<dynamic, dynamic> jsonMap,
  ) async {
    isLoading = true;

    isConnectedToInternet = await Utils.checkInternetConnectivity();

    _specification = await _repo!.postSpecification(
        jsonMap, isConnectedToInternet, PsStatus.PROGRESS_LOADING);

    return _specification;
  }

  Future<dynamic> postDeleteSpecification(
    Map<dynamic, dynamic> jsonMap,
  ) async {
    isLoading = true;

    isConnectedToInternet = await Utils.checkInternetConnectivity();

    _deleteSpecification = await _repo!.postDeleteSpecification(
        jsonMap, isConnectedToInternet, PsStatus.PROGRESS_LOADING);

    return _deleteSpecification;
  }
}
