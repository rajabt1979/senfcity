import 'dart:async';

import 'package:flutter/material.dart';
import 'package:fluttercity/api/common/ps_resource.dart';
import 'package:fluttercity/api/common/ps_status.dart';
import 'package:fluttercity/provider/common/ps_provider.dart';
import 'package:fluttercity/repository/item_collection_repository.dart';
import 'package:fluttercity/utils/utils.dart';
import 'package:fluttercity/viewobject/item_collection_header.dart';

class ItemCollectionProvider extends PsProvider {
  ItemCollectionProvider(
      {@required ItemCollectionRepository? repo, int limit = 0})
      : super(repo, limit) {
    _repo = repo;
    print('ItemCollection Provider: $hashCode');
    Utils.checkInternetConnectivity().then((bool onValue) {
      isConnectedToInternet = onValue;
    });

    itemCollectionListStream =
        StreamController<PsResource<List<ItemCollectionHeader>>>.broadcast();
    subscription = itemCollectionListStream.stream.listen((dynamic resource) {
      //Utils.psPrint("ItemCollectionHeader Provider : ");

      updateOffset(resource.data.length);

      _itemCollectionList = resource;

      if (resource.status != PsStatus.BLOCK_LOADING &&
          resource.status != PsStatus.PROGRESS_LOADING) {
        isLoading = false;
      }

      if (!isDispose) {
        notifyListeners();
      }
    });

    itemCollectionStream =
        StreamController<PsResource<ItemCollectionHeader>>.broadcast();
    subscriptionById = itemCollectionStream.stream.listen((dynamic resource) {
      _itemCollection = resource;

      if (resource.status != PsStatus.BLOCK_LOADING &&
          resource.status != PsStatus.PROGRESS_LOADING) {
        isLoading = false;
      }

      if (!isDispose) {
        notifyListeners();
      }
    });
  }

  ItemCollectionRepository? _repo;

  PsResource<List<ItemCollectionHeader>> _itemCollectionList =
      PsResource<List<ItemCollectionHeader>>(
          PsStatus.NOACTION, '', <ItemCollectionHeader>[]);

  PsResource<ItemCollectionHeader> _itemCollection =
      PsResource<ItemCollectionHeader>(PsStatus.NOACTION, '', null);

  PsResource<List<ItemCollectionHeader>> get itemCollectionList =>
      _itemCollectionList;

  PsResource<ItemCollectionHeader> get itemCollection => _itemCollection;

 late StreamSubscription<PsResource<List<ItemCollectionHeader>>> subscription;
 late StreamController<PsResource<List<ItemCollectionHeader>>>
      itemCollectionListStream;

 late StreamSubscription<PsResource<ItemCollectionHeader>> subscriptionById;
 late StreamController<PsResource<ItemCollectionHeader>> itemCollectionStream;
  @override
  void dispose() {
    subscription.cancel();
    subscriptionById.cancel();
    isDispose = true;
    print('Item Collection Provider Dispose: $hashCode');
    super.dispose();
  }

  Future<dynamic> loadItemCollectionList() async {
    isLoading = true;
    isConnectedToInternet = await Utils.checkInternetConnectivity();
    await _repo!.getItemCollectionList(itemCollectionListStream,
        isConnectedToInternet, limit, offset, PsStatus.PROGRESS_LOADING);
  }

  Future<dynamic> nextItemCollectionList() async {
    isConnectedToInternet = await Utils.checkInternetConnectivity();
    if (!isLoading && !isReachMaxData) {
      super.isLoading = true;

      await _repo!.getNextPageItemCollectionList(itemCollectionListStream,
          isConnectedToInternet, limit, offset, PsStatus.PROGRESS_LOADING);
    }
  }

  Future<void> resetItemCollectionList() async {
    isConnectedToInternet = await Utils.checkInternetConnectivity();
    isLoading = true;

    updateOffset(0);

    await _repo!.getItemCollectionList(itemCollectionListStream,
        isConnectedToInternet, limit, offset, PsStatus.PROGRESS_LOADING);

    isLoading = false;
  }
}
