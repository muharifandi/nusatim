package com.nusatim.partner.features.home.ui

import androidx.test.ext.junit.runners.AndroidJUnit4
import com.nusatim.partner.features.home.robot.dashboard
import org.junit.Test
import org.junit.runner.RunWith

@RunWith(AndroidJUnit4::class)
class HomeFragmentTest {

    @Test
    fun testDashboardComponentsAreDisplayed() {
        dashboard {
            verifyDashboardIsDisplayed()
        }
    }
}
