package com.nusatim.partner.core.testing.robot

import androidx.test.espresso.Espresso.onView
import androidx.test.espresso.ViewInteraction
import androidx.test.espresso.action.ViewActions.*
import androidx.test.espresso.assertion.ViewAssertions.matches
import androidx.test.espresso.contrib.RecyclerViewActions
import androidx.test.espresso.matcher.ViewMatchers.*
import androidx.recyclerview.widget.RecyclerView
import org.hamcrest.CoreMatchers.allOf

abstract class BaseRobot {
    protected fun fillEditText(resId: Int, text: String): ViewInteraction =
        onView(withId(resId)).perform(replaceText(text), closeSoftKeyboard())

    protected fun clickButton(resId: Int): ViewInteraction =
        onView(withId(resId)).perform(click())

    protected fun viewIsDisplayed(resId: Int): ViewInteraction =
        onView(withId(resId)).check(matches(isDisplayed()))

    protected fun viewHasText(resId: Int, text: String): ViewInteraction =
        onView(withId(resId)).check(matches(withText(text)))

    protected fun clickRecyclerViewItem(resId: Int, position: Int): ViewInteraction =
        onView(withId(resId)).perform(
            RecyclerViewActions.actionOnItemAtPosition<RecyclerView.ViewHolder>(
                position,
                click()
            )
        )
}
