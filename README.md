# Elasticsearch Widget

## Config

```
-
    id: elasticsearch1
    provider: \StpBoard\Elasticsearch\ElasticsearchControllerProvider
    refresh: 60
    width: 4
    params:
      name: NAME_TO_BE_DISPLAYED
      apiUrl: MEASUREMENT_API_URL
      action: ACTION
      index: ELASTICSEARCH_INDEX_TO_MONITOR
      since: now-3h/h
```

### Available actions are:

* document_count
* storage_size
* search_average_time
* index_average_time

Parameter ```since``` is optional - default value is ```now-3h/h```. It describes time period from which data should be displayed.

Parameter ```apiUrl``` defines API url for measurements.

Parameter ```index``` defines Elasticsearch index to monitor.
