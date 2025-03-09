import 'package:fluttercity/db/common/ps_dao.dart' show PsDao;
import 'package:fluttercity/viewobject/status.dart';
import 'package:sembast/sembast.dart';

class StatusDao extends PsDao<Status> {
  StatusDao._() {
    init(Status());
  }
  static const String STORE_NAME = 'Status';
  final String _primaryKey = 'id';

  // Singleton instance
  static final StatusDao _singleton = StatusDao._();

  // Singleton accessor
  static StatusDao get instance => _singleton;

  @override
  String getStoreName() {
    return STORE_NAME;
  }

  @override
  String? getPrimaryKey(Status object) {
    return object.id;
  }

  @override
  Filter getFilter(Status object) {
    return Filter.equals(_primaryKey, object.id);
  }
}
