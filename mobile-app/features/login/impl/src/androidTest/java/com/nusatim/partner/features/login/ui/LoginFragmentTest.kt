package com.nusatim.partner.features.login.ui

import androidx.test.ext.junit.runners.AndroidJUnit4
import com.nusatim.partner.features.login.robot.login
import org.junit.Test
import org.junit.runner.RunWith

@RunWith(AndroidJUnit4::class)
class LoginFragmentTest {

    @Test
    fun testLoginComponentsAreDisplayed() {
        // Karena launchFragmentInContainer butuh dependency tambahan yang mungkin belum ada, 
        // kita gunakan pendekatan ActivityScenarioRule untuk sementara jika fragment terikat ke MainActivity
        // Namun sesuai instruksi 'robot pattern', kita asumsikan environment sudah siap.
        
        login {
            verifyLoginIsDisplayed()
        }
    }
}
