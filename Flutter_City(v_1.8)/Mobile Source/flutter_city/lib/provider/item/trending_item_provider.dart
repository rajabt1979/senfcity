import 'dart:async';

import 'package:flutter/material.dart';
import 'package:fluttercity/api/common/ps_resource.dart';
import 'package:fluttercity/api/common/ps_status.dart';
import 'package:fluttercity/provider/common/ps_provider.dart';
import 'package:fluttercity/repository/item_repository.dart';
import 'package:fluttercity/utils/utils.dart';
import 'package:fluttercity/viewobject/holder/item_parameter_holder.dart';
import 'package:fluttercity/viewobject/item.dart';

class TrendingItemProvider extends PsProvider {
  TrendingItemProvider({@required ItemRepository? repo, int limit = 0})
      : super(repo, limit) {
    _repo = repo;
    print('TrendingItemProvider : $hashCode');
    Utils.checkInternetConnectivity().then((bool onValue) {
      isConnectedToInternet = onValue;
    });

    itemListStream = StreamController<PsResource<List<Item>>>.broadcast();
    subscription =
        itemListStream.stream.listen((PsResource<List<Item>> resource) {
      updateOffset(resource.data!.length);

      _itemList = Utils.removeDuplicateObj<Item>(resource);

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
  PsResource<List<Item>> _itemList =
      PsResource<List<Item>>(PsStatus.NOACTION, '', <Item>[]);

  PsResource<List<Item>> get itemList => _itemList;
 late StreamSubscription<PsResource<List<Item>>> subscription;
 late StreamController<PsResource<List<Item>>> itemListStream;

  @override
  void dispose() {
    subscription.cancel();
    isDispose = true;
    print('Trending Item Provider Dispose: $hashCode');
    super.dispose();
  }

  Future<dynamic> loadItemList(int limit) async {
    isLoading = true;

    isConnectedToInternet = await Utils.checkInternetConnectivity();

    await _repo!.getItemList(
        itemListStream,
        isConnectedToInternet,
        limit,
        offset,
        PsStatus.PROGRESS_LOADING,
        ItemParameterHolder().getTrendingParameterHolder());
  }

  Future<dynamic> nextItemList() async {
    isConnectedToInternet = await Utils.checkInternetConnectivity();

    if (!isLoading && !isReachMaxData) {
      super.isLoading = true;

      await _repo!.getItemList(
          itemListStream,
          isConnectedToInternet,
          limit,
          offset,
          PsStatus.PROGRESS_LOADING,
          ItemParameterHolder().getTrendingParameterHolder());
    }
  }

  Future<void> resetTrendingItemList() async {
    isConnectedToInternet = await Utils.checkInternetConnectivity();

    updateOffset(0);

    isLoading = true;
    await _repo!.getItemList(
        itemListStream,
        isConnectedToInternet,
        limit,
        offset,
        PsStatus.PROGRESS_LOADING,
        ItemParameterHolder().getTrendingParameterHolder());
  }
}
