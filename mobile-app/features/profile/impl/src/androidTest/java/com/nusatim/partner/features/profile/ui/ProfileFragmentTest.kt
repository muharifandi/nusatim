package com.nusatim.partner.features.profile.ui

import androidx.fragment.app.testing.launchFragmentInContainer
import androidx.test.ext.junit.runners.AndroidJUnit4
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.PartnerResponse
import com.nusatim.partner.features.profile.domain.usecase.GetProfileUseCase
import com.nusatim.partner.features.profile.robot.profile
import com.nusatim.partner.features.profile.ui.main.ProfileFragment
import io.mockk.coEvery
import io.mockk.mockk
import kotlinx.coroutines.flow.flowOf
import org.junit.Test
import org.junit.runner.RunWith

@RunWith(AndroidJUnit4::class)
class ProfileFragmentTest {

    private val getProfileUseCase: GetProfileUseCase = mockk()

    @Test
    fun displayProfileData() {
        val partner = PartnerResponse(
            id = 1,
            name = "Budi Santoso",
            email = "budi@mail.com",
            status = "approved",
            level = null,
            rejectionReason = null,
            bankName = "BCA",
            bankAccountNumber = "123",
            bankAccountHolder = "Budi",
            emailNotificationsEnabled = true,
            agreementAcceptedAt = "",
            profilePhotoUrl = "",
            ktpUrl = "",
            npwpUrl = null,
            createdAt = ""
        )
        
        coEvery { getProfileUseCase() } returns flowOf(ResultState.Success(partner))

        launchFragmentInContainer<ProfileFragment>()

        profile {
            verifyName("Budi Santoso")
            verifyEmail("budi@mail.com")
        }
    }
}
