import 'dart:async';

import 'package:flutter/cupertino.dart';
import 'package:fluttercity/api/common/ps_resource.dart';
import 'package:fluttercity/api/common/ps_status.dart';
import 'package:fluttercity/provider/common/ps_provider.dart';
import 'package:fluttercity/repository/item_repository.dart';
import 'package:fluttercity/utils/utils.dart';
import 'package:fluttercity/viewobject/api_status.dart';
import 'package:fluttercity/viewobject/common/ps_value_holder.dart';

class TouchCountProvider extends PsProvider {
  TouchCountProvider(
      {@required ItemRepository? repo,
      @required this.psValueHolder,
      int limit = 0})
      : super(repo, limit) {
    _repo = repo;

    print('TouchCount Item Provider: $hashCode');

    Utils.checkInternetConnectivity().then((bool onValue) {
      isConnectedToInternet = onValue;
    });
  }

  ItemRepository?_repo;
  PsValueHolder? psValueHolder;

  PsResource<ApiStatus> _apiStatus =
      PsResource<ApiStatus>(PsStatus.NOACTION, '', null);
  PsResource<ApiStatus> get user => _apiStatus;

  @override
  void dispose() {
    isDispose = true;
    print('TouchCount Item Provider Dispose: $hashCode');
    super.dispose();
  }

  Future<dynamic> postTouchCount(
    Map<dynamic, dynamic> jsonMap,
  ) async {
    isLoading = true;

    isConnectedToInternet = await Utils.checkInternetConnectivity();

    _apiStatus = await _repo!.postTouchCount(
        jsonMap, isConnectedToInternet, PsStatus.PROGRESS_LOADING);

    return _apiStatus;
  }
}
