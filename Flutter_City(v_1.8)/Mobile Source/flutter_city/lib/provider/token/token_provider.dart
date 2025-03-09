import 'dart:async';

import 'package:flutter/cupertino.dart';
import 'package:fluttercity/api/common/ps_resource.dart';
import 'package:fluttercity/api/common/ps_status.dart';
import 'package:fluttercity/provider/common/ps_provider.dart';
import 'package:fluttercity/repository/token_repository.dart';
import 'package:fluttercity/utils/utils.dart';
import 'package:fluttercity/viewobject/api_status.dart';

class TokenProvider extends PsProvider {
  TokenProvider({@required TokenRepository? repo, int limit = 0})
      : super(repo, limit) {
    _repo = repo;
    print('Token Provider: $hashCode');

    Utils.checkInternetConnectivity().then((bool onValue) {
      isConnectedToInternet = onValue;
    });
  }

  TokenRepository? _repo;
  @override
  void dispose() {
    isDispose = true;
    print('Token Provider Dispose: $hashCode');
    super.dispose();
  }

  Future<dynamic> loadToken() async {
    isLoading = true;
    isConnectedToInternet = await Utils.checkInternetConnectivity();
    final PsResource<ApiStatus> _resource =
        await _repo!.getToken(isConnectedToInternet, PsStatus.SUCCESS);
    return _resource;
  }
}
