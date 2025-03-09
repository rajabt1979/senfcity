import 'dart:async';

import 'package:flutter/material.dart';
import 'package:fluttercity/api/common/ps_resource.dart';
import 'package:fluttercity/api/common/ps_status.dart';
import 'package:fluttercity/provider/common/ps_provider.dart';
import 'package:fluttercity/repository/item_repository.dart';
import 'package:fluttercity/utils/utils.dart';
import 'package:fluttercity/viewobject/api_status.dart';
import 'package:fluttercity/viewobject/common/ps_value_holder.dart';
import 'package:fluttercity/viewobject/holder/item_parameter_holder.dart';
import 'package:fluttercity/viewobject/item.dart';

class RejectedItemProvider extends PsProvider {
  RejectedItemProvider(
      {@required ItemRepository? repo, this.psValueHolder, int limit = 0})
      : super(repo, limit) {
    _repo = repo;
    print('RejectedItemProvider : $hashCode');
    Utils.checkInternetConnectivity().then((bool onValue) {
      isConnectedToInternet = onValue;
    });

    itemListStream = StreamController<PsResource<List<Item>>>.broadcast();
    subscription =
        itemListStream.stream.listen((PsResource<List<Item>> resource) {
      updateOffset(resource.data!.length);

      _itemList = resource;

      if (resource.status != PsStatus.BLOCK_LOADING &&
          resource.status != PsStatus.PROGRESS_LOADING) {
        isLoading = false;
      }

      if (!isDispose) {
        notifyListeners();
      }
    });
  }
  ItemRepository? _repo;
  PsValueHolder? psValueHolder;
  ItemParameterHolder addedUserParameterHolder =
      ItemParameterHolder().getRejectedItemParameterHolder();
  PsResource<List<Item>> _itemList =
      PsResource<List<Item>>(PsStatus.NOACTION, '', <Item>[]);

  PsResource<ApiStatus> _deleteItem =
      PsResource<ApiStatus>(PsStatus.NOACTION, '', null);

  PsResource<List<Item>> get itemList => _itemList;
 late StreamSubscription<PsResource<List<Item>>> subscription;
late  StreamController<PsResource<List<Item>>> itemListStream;
  @override
  void dispose() {
    subscription.cancel();
    isDispose = true;
    print('Added Item Provider Dispose: $hashCode');
    super.dispose();
  }

  Future<dynamic> loadItemList(
      String loginUserId, ItemParameterHolder holder) async {
    isLoading = true;

    isConnectedToInternet = await Utils.checkInternetConnectivity();

    await _repo!.getItemListByUserId(
        itemListStream,
        loginUserId,
        isConnectedToInternet,
        limit,
        offset,
        PsStatus.PROGRESS_LOADING,
        holder);
  }

  Future<dynamic> nextItemList(
      String loginUserId, ItemParameterHolder holder) async {
    isConnectedToInternet = await Utils.checkInternetConnectivity();

    if (!isLoading && !isReachMaxData) {
      super.isLoading = true;

      await _repo!.getNextPageItemListByUserId(
          itemListStream,
          loginUserId,
          isConnectedToInternet,
          limit,
          offset,
          PsStatus.PROGRESS_LOADING,
          holder);
    }
  }

  Future<void> resetItemList(
      String loginUserId, ItemParameterHolder holder) async {
    isConnectedToInternet = await Utils.checkInternetConnectivity();
    isLoading = true;

    updateOffset(0);

    await _repo!.getItemListByUserId(
        itemListStream,
        loginUserId,
        isConnectedToInternet,
        limit,
        offset,
        PsStatus.PROGRESS_LOADING,
        holder);

    isLoading = false;
  }

  Future<dynamic> postDeleteItem(
    Map<dynamic, dynamic> jsonMap,
  ) async {
    isLoading = true;

    isConnectedToInternet = await Utils.checkInternetConnectivity();

    _deleteItem = await _repo!.postDeleteItem(
        jsonMap, isConnectedToInternet, PsStatus.PROGRESS_LOADING);

    return _deleteItem;
  }
}
