import 'dart:async';

import 'package:flutter/material.dart';
import 'package:fluttercity/api/common/ps_resource.dart';
import 'package:fluttercity/api/common/ps_status.dart';
import 'package:fluttercity/provider/common/ps_provider.dart';
import 'package:fluttercity/repository/item_repository.dart';
import 'package:fluttercity/utils/utils.dart';
import 'package:fluttercity/viewobject/common/ps_value_holder.dart';
import 'package:fluttercity/viewobject/download_item.dart';
import 'package:fluttercity/viewobject/item.dart';
import 'package:url_launcher/url_launcher.dart';

class ItemDetailProvider extends PsProvider {
  ItemDetailProvider(
      {@required ItemRepository? repo,
      @required this.psValueHolder,
      int limit = 0})
      : super(repo, limit) {
    _repo = repo;
    print('ItemDetailProvider : $hashCode');

    Utils.checkInternetConnectivity().then((bool onValue) {
      isConnectedToInternet = onValue;
    });

    itemDetailStream = StreamController<PsResource<Item>>.broadcast();
    subscription = itemDetailStream.stream.listen((PsResource<Item> resource) {
      _item = resource;

      if (resource.status != PsStatus.BLOCK_LOADING &&
          resource.status != PsStatus.PROGRESS_LOADING) {
        isLoading = false;
      }

      if (!isDispose) {
        //if (_item != null) {
          notifyListeners();
       // }
      }
    });
  }

  ItemRepository? _repo;
  PsValueHolder? psValueHolder;

  PsResource<Item> _item = PsResource<Item>(PsStatus.NOACTION, '', null);

  PsResource<Item> get itemDetail => _item;
 late StreamSubscription<PsResource<Item>> subscription;
 late StreamController<PsResource<Item>> itemDetailStream;
  @override
  void dispose() {
    subscription.cancel();
    isDispose = true;
    print('Item Detail Provider Dispose: $hashCode');
    super.dispose();
  }

  void updateItem(Item item) {
    _item.data = item;
  }

  Future<dynamic> loadItem(
    String itemId,
    String loginUserId,
  ) async {
    isLoading = true;
    isConnectedToInternet = await Utils.checkInternetConnectivity();

    await _repo!.getItemDetail(itemDetailStream, itemId, loginUserId,
        isConnectedToInternet, PsStatus.BLOCK_LOADING);
  }

  Future<dynamic> loadItemForFav(
    String itemId,
    String loginUserId,
  ) async {
    isLoading = true;
    isConnectedToInternet = await Utils.checkInternetConnectivity();

    await _repo!.getItemDetailForFav(itemDetailStream, itemId, loginUserId,
        isConnectedToInternet, PsStatus.PROGRESS_LOADING);
  }

  Future<dynamic> nextItem(
    String itemId,
    String loginUserId,
  ) async {
    if (!isLoading && !isReachMaxData) {
      super.isLoading = true;
      isConnectedToInternet = await Utils.checkInternetConnectivity();
      await _repo!.getItemDetail(itemDetailStream, itemId, loginUserId,
          isConnectedToInternet, PsStatus.PROGRESS_LOADING);
    }
  }

  Future<void> resetItemDetail(
    String itemId,
    String loginUserId,
  ) async {
    isLoading = true;
    isConnectedToInternet = await Utils.checkInternetConnectivity();
    await _repo!.getItemDetail(itemDetailStream, itemId, loginUserId,
        isConnectedToInternet, PsStatus.BLOCK_LOADING);

    isLoading = false;
  }

  PsResource<List<DownloadItem>> _downloadItem =
      PsResource<List<DownloadItem>>(PsStatus.NOACTION, '', null);
  PsResource<List<DownloadItem>> get user => _downloadItem;

  Future<dynamic> postDownloadItemList(Map<dynamic, dynamic> jsonMap) async {
    isLoading = true;

    isConnectedToInternet = await Utils.checkInternetConnectivity();

    _downloadItem = await _repo!.postDownloadItemList(
        jsonMap, isConnectedToInternet, PsStatus.PROGRESS_LOADING);

    return _downloadItem;
  }

  Future<void> launchWhatsApp() async {
    if (itemDetail.data!.whatsapp != null) {
      launchUrl(Uri.parse('https://wa.me/${itemDetail.data!.whatsapp}'));
    }
  }

  Future<void> launchCallPhone() async {
    if (itemDetail.data!.phone1 != null) {
      launchUrl(Uri.parse('tel://${itemDetail.data!.phone1}'));
    }
  }

  Future<void> launchMessenger() async {
    if (itemDetail.data!.messenger != null) {
      launchUrl(Uri.parse('http://m.me/${itemDetail.data!.messenger}'));
    }
  }
}
