import 'dart:async';

import 'package:flutter/material.dart';
import 'package:fluttercity/api/common/ps_resource.dart';
import 'package:fluttercity/api/common/ps_status.dart';
import 'package:fluttercity/provider/common/ps_provider.dart';
import 'package:fluttercity/repository/rating_repository.dart';
import 'package:fluttercity/utils/utils.dart';
import 'package:fluttercity/viewobject/rating.dart';

class RatingProvider extends PsProvider {
  RatingProvider({@required RatingRepository? repo, int limit = 0})
      : super(repo, limit) {
    _repo = repo;
    print('Rating Provider: $hashCode');

    Utils.checkInternetConnectivity().then((bool onValue) {
      isConnectedToInternet = onValue;
    });

    ratingListStream = StreamController<PsResource<List<Rating>>>.broadcast();
    subscription =
        ratingListStream.stream.listen((PsResource<List<Rating>> resource) {
      updateOffset(resource.data!.length);

      _ratingList = resource;

      if (resource.status != PsStatus.BLOCK_LOADING &&
          resource.status != PsStatus.PROGRESS_LOADING) {
        isLoading = false;
      }

      if (!isDispose) {
        notifyListeners();
      }
    });
  }

  RatingRepository? _repo;

  PsResource<Rating> _rating = PsResource<Rating>(PsStatus.NOACTION, '', null);
  PsResource<Rating> get user => _rating;

  PsResource<List<Rating>> _ratingList =
      PsResource<List<Rating>>(PsStatus.NOACTION, '', <Rating>[]);

  PsResource<List<Rating>> get ratingList => _ratingList;
 late StreamSubscription<PsResource<List<Rating>>> subscription;
 late StreamController<PsResource<List<Rating>>> ratingListStream;
  StreamController<PsResource<Rating>>? ratingStream;

  @override
  void dispose() {
    subscription.cancel();
    isDispose = true;
    print('Rating Provider Dispose: $hashCode');
    super.dispose();
  }

  Future<dynamic> loadRatingList(String itemId) async {
    isLoading = true;

    isConnectedToInternet = await Utils.checkInternetConnectivity();
    await _repo!.getAllRatingList(ratingListStream, itemId,
        isConnectedToInternet, limit, offset, PsStatus.PROGRESS_LOADING);
  }

  Future<dynamic> nextRatingList(String itemId) async {
    isConnectedToInternet = await Utils.checkInternetConnectivity();

    if (!isLoading && !isReachMaxData) {
      super.isLoading = true;
      await _repo!.getNextPageRatingList(ratingListStream, itemId,
          isConnectedToInternet, limit, offset, PsStatus.PROGRESS_LOADING);
    }
  }

  Future<void> resetRatingList(String itemId) async {
    isConnectedToInternet = await Utils.checkInternetConnectivity();
    isLoading = true;

    updateOffset(0);

    await _repo!.getAllRatingList(ratingListStream, itemId,
        isConnectedToInternet, limit, offset, PsStatus.PROGRESS_LOADING);

    isLoading = false;
  }

  Future<dynamic> refreshRatingList(String itemId) async {
    isLoading = true;

    isConnectedToInternet = await Utils.checkInternetConnectivity();
    await _repo!.getAllRatingList(ratingListStream, itemId,
        isConnectedToInternet, limit, 0, PsStatus.PROGRESS_LOADING,
        isNeedDelete: false);
  }

  Future<dynamic> postRating(
    Map<dynamic, dynamic> jsonMap,
  ) async {
    isLoading = true;

    isConnectedToInternet = await Utils.checkInternetConnectivity();

    _rating =
        await _repo!.postRating(ratingStream, jsonMap, isConnectedToInternet);

    return _rating;
  }
}
