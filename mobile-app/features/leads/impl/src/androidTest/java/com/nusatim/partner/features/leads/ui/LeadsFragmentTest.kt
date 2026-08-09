package com.nusatim.partner.features.leads.ui

import androidx.test.ext.junit.runners.AndroidJUnit4
import com.nusatim.partner.features.leads.robot.leads
import org.junit.Test
import org.junit.runner.RunWith

@RunWith(AndroidJUnit4::class)
class LeadsFragmentTest {

    @Test
    fun testLeadsListIsDisplayed() {
        leads {
            verifyLeadsListIsDisplayed()
        }
    }
}
