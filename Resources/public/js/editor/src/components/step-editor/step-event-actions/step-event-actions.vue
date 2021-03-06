<template>

  <div>
    <div v-for="formEvent in formEvents" v-if="formEventHasActions(formEvent)" :key="formEvent.name">
      <strong>{{ formEvent.name }}</strong>
      <step-event-action-configuration
        :key="index"
        v-for="(action, index) in getFormEventStepEventActions(formEvent)"
        :action="action.action"
        :name="action.name"
        :parameters="action.parameters"
        @remove="removeStepEventAction(formEvent, index)"
        @updateName="updateStepEventActionName($event, formEvent, index)"
        @updateOption="updateStepEventActionOption($event, formEvent, index)"
      ></step-event-action-configuration>
    </div>
  </div>

</template>

<script>

import stepEditorStepEventActionConfiguration from './step-event-action-configuration.vue';

export default {

  props: ['name'],

  computed: {
    stepEventActions: function () {
      return this.$store.getters.getStepEventActions(this.name);
    },
    formEvents: function () {
      return this.$store.getters.getFormEvents;
    }
  },

  components: {
    'step-event-action-configuration': stepEditorStepEventActionConfiguration
  },

  methods: {

    /**
     * Get the step events action triggered by a form event
     *
     * @param formEvent
     *
     * @return []
     */
    getFormEventStepEventActions: function (formEvent) {
      for (var stepEventAction in this.stepEventActions) {
        if (this.stepEventActions.hasOwnProperty(stepEventAction) && stepEventAction === formEvent.name) {
          return this.stepEventActions[stepEventAction];
        }
      }

      return null;
    },

    /**
     * Check if a form event contains step event actions
     *
     * @param formEvent
     */
    formEventHasActions: function (formEvent) {
      return this.stepEventActions && this.stepEventActions[formEvent.name];
    },

    /**
     * Remove a step event action
     *
     * @param formEvent
     * @param actionIndex
     */
    removeStepEventAction: function (formEvent, actionIndex) {
      this.$store.commit('removeStepEventAction', {
        stepName: this.name,
        formEventName: formEvent.name,
        actionIndex: actionIndex
      });
    },

    /**
     * Update the name of a step event action
     *
     * @param name
     * @param formEvent
     * @param actionIndex
     */
    updateStepEventActionName: function (name, formEvent, actionIndex) {
      this.$store.commit('updateStepEventActionName', {
        stepName: this.name,
        formEventName: formEvent.name,
        actionIndex: actionIndex,
        actionName: name
      });
    },

    /**
     * Update an option of a step event action
     *
     * @param option
     * @param formEvent
     * @param actionIndex
     */
    updateStepEventActionOption: function (option, formEvent, actionIndex) {
      this.$store.commit('updateStepEventActionOption', {
        stepName: this.name,
        formEventName: formEvent.name,
        actionIndex: actionIndex,
        option: option
      });
    }

  }
};

</script>
