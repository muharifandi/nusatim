package com.nusatim.partner.features.register.ui

import androidx.test.ext.junit.runners.AndroidJUnit4
import com.nusatim.partner.core.testing.robot.register
import org.junit.Test
import org.junit.runner.RunWith

@RunWith(AndroidJUnit4::class)
class RegisterFragmentTest {

    @Test
    fun testRegisterComponentsAreDisplayed() {
        register {
            verifyRegisterIsDisplayed()
        }
    }
}
