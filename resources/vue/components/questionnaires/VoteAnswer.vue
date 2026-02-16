<!--
$answers = $vote->questiondata['options'];
$indexMap = count($answers) ? range(0, count($answers) - 1) : [];
if ($vote->questiondata['randomize']) {
shuffle($indexMap);
}

$response = $vote->getMyAnswer();
$responseData = $response['answerdata'] ? $response['answerdata']->getArrayCopy() : [];
-->

<template>
    {{ $gettext('Ich bin eine Auswahlfrage') }}
    {{ question.questiondata.options }}



</template>





<script>
export default {
    name: 'VoteAnswer',
    props: {
        question: Array
    },
};


</script>

