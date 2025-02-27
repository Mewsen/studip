---
id: activity-api
title: Activity API
sidebar_label: Activity API
---

# Activity API

Mit Stud.IP Version 3.5 wurde eine neue API zum Erzeugen, Darstellen und Filtern von kontextrelevanten Aktivitäten eingeführt. Diese API kann u.a. dafür genutzt werden um Nutzern einen schnellen Überblick über die für ihn relevanten Information/Aktivitäten zu geben.

## Activity-Streams

Der Activity-Stream befindet sich auf oberster Ebene der API. Möchte man einen Activity-Stream haben:
 
```php
$stream = new \Studip\Activity\Stream($observer_id, $contexts, $filter);
```

Wobei der Parameter `$observer_id` die Nutzer-ID des Betrachters ist, `$contexts` ein Kontext oder ein Array der zu betrachtenden Aktivitätskontexte (z.B. eine Veranstaltung, ein Institut oder ein Nutzer) und `$filter` ein entsprechendes Filter-Objekt ist.


Möchte man diesen ausgeben:

 
```php
foreach($stream->asArray() as $key => $activity) {
    echo $activity;
}
```

#### Filter
Um den Zugriff auf bestimmte Aktivitäten oder Zeiräume zu fokussieren, können entsprechenden Filter-Objekte definiert werden. Filter-Objekte bestehen aus einem Start-, Endpunkt und einem Aktivitätstyp, welcher einen entsprechenden Activity-Provider beschreibt.


## Activity-Kontexte
Der Activity-Stream bekommt die Aktivitäten über Activity-Kontexte bereitgestellt. Kontexte beschreiben die Bereiche in Stud.IP, in denen potentielle Aktivitäten generiert werden können.

Beispiele für Kontexte:

Veranstaltungen
Einrichtungen
Systemweiter Kontext
Nutzerbezogener Kontext

Klassenbeschreibung des Activity-Kontext:

```php
abstract class Context
{
    protected
        $provider;

    /**
     * return array, listing all active providers in this context
     *
     * @return array
     */
    abstract protected function getProvider();

    /**
     * get id denoting the context (user_id, course_id, institute_id, ...)
     *
     * @return string
     */
    abstract public function getRangeId();

    /**
     * get type of context (f.e. user, system, course, institute, ...)
     *
     * @return string
     */
    abstract protected function getContextType();

    /**
     * get list of activities as array for the current context
     *
     * @param string $observer_id
     * @param \Studip\Activity\Filter $filter
     *
     * @return array
     */
    public function getActivities($observer_id, Filter $filter)
    {
        $providers = $this->filterProvider($this->getProvider(), $filter);

        $activities = Activity::findBySQL('context = ? AND context_id = ?  AND mkdate >= ? AND mkdate <= ? ORDER BY mkdate DESC',
            array($this->getContextType(), $this->getRangeId(), $filter->getStartDate(), $filter->getEndDate()));

        foreach ($activities as $key => $activity) {
            if (isset($providers[$activity->provider])) {                        // provider is available
                $providers[$activity->provider]->getActivityDetails($activity);
            } else {
                unset($activities[$key]);
            }
        }

        return array_flatten($activities);
    }

    /**
     *
     * @param type $provider
     */
    protected function addProvider($provider)
    {
        $class_name = 'Studip\Activity\\' . ucfirst($provider) . 'Provider';

        $reflectionClass = new \ReflectionClass($class_name);
        $this->provider[$provider] =  $reflectionClass->newInstanceArgs();
    }

    /**
     *
     * @param type $providers
     * @param \Studip\Activity\Filter $filter
     * @return type
     */
    protected function filterProvider($providers, Filter $filter)
    {
        $filtered_providers = array();

        if (is_null($filter->getType())) {
            $filtered_providers = $providers;
        } else {
            foreach($providers as $provider) {
                $filtered_class = 'Studip\Activity\\' . ucfirst($filter->getType()) . 'Provider';

                if ($provider instanceof $filtered_class) {
                    $filtered_providers[] =  $provider;
                }
            }
        }

        return $filtered_providers;

    }
}
```

## Activity-Provider
Orthogonal zu diesen Kontexten existieren Activity-Provider. Diese aggregieren bestimmte Activities. Beispiele für Activity-Provider:

*Forum
*Blubber
*Dateibereich
*Nachrichten
*News
*Teilnehmer
*Wiki
*Ablaufplan
*Liteaturverwaltung

#### Das Activity-Provider-Interface

Möchte man einen neuen Activity-Provider implementieren, muss man das entsprechende Interface berücksichtigen, welches die Methoden `getActivityDetails()` und `getLexicalField()` voraussetzt.

```php
interface ActivityProvider
{
    /**
     * Fill in the url, route and any lengthy content for the passed activity
     *
     * @param Studip\Activity\Activity $activity
     */
    public function getActivityDetails(&$activity);

    /**
     * Human readable name for the current provider to be used in the activity-title
     */
    public static function getLexicalField();
}
```

Um eine persistente Datenhaltung der Aktivitäten sicherzustellen, wird empfohlen zusätzlich die Methode `postActivity()` zu implementieren, welche unter Verwendung entsprechender Observer aus dem NotificationCenter Daten in die Activity-Tabelle speichert.

#### Die Klasse ActivityObserver
Die Klasse ActivityObserver sorgt dafür, dass relevante Observer aus dem NotifactionCenter geladen werden. Hier können nach Bedarf weitere Observer für z.B. neue Provider abgelegt werden.


## Activities

## Activity-Feed Widget

Das ActivityFeed-Widget wird als Portalplugin implementiert. Neben der Ansicht des Activity-Streams bietet das Widget dem Nutzer auch die Möglichkeit den Stream individuell zu Filtern.

## REST-Route
Als alternative Zugriffsmöglichkeit werden der Stud.IP REST-API folgende neue Routen hinzugefügt:

```php
/user/:user_id/activitystream # get - Ausgabe des Streams
```

Die Route kann durch Angabe weiterer Queryparameter gefiltert werden.
