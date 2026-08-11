package com.nusatim.partner.features.profile.ui.main

import app.cash.turbine.test
import com.nusatim.partner.core.common.security.SessionManager
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.PartnerResponse
import com.nusatim.partner.features.profile.domain.usecase.*
import com.nusatim.partner.features.profile.ui.main.state.ProfileEffect
import com.nusatim.partner.features.profile.ui.main.state.ProfileIntent
import io.mockk.coEvery
import io.mockk.every
import io.mockk.mockk
import io.mockk.verify
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.ExperimentalCoroutinesApi
import kotlinx.coroutines.flow.flowOf
import kotlinx.coroutines.test.UnconfinedTestDispatcher
import kotlinx.coroutines.test.resetMain
import kotlinx.coroutines.test.runTest
import kotlinx.coroutines.test.setMain
import org.junit.After
import org.junit.Assert.assertEquals
import org.junit.Before
import org.junit.Test
import java.io.File

@OptIn(ExperimentalCoroutinesApi::class)
class ProfileViewModelTest {

    private val getProfileUseCase: GetProfileUseCase = mockk()
    private val logoutUseCase: LogoutUseCase = mockk()
    private val updateProfileUseCase: UpdateProfileUseCase = mockk()
    private val updateKycUseCase: UpdateKycUseCase = mockk()
    private val updatePasswordUseCase: UpdatePasswordUseCase = mockk()
    private val deleteAccountUseCase: DeleteAccountUseCase = mockk()
    private val sessionManager: SessionManager = mockk(relaxed = true)

    private lateinit var viewModel: ProfileViewModel
    private val testDispatcher = UnconfinedTestDispatcher()

    @Before
    fun setUp() {
        Dispatchers.setMain(testDispatcher)

        // Mock initial LoadProfile in init
        coEvery { getProfileUseCase() } returns flowOf(ResultState.Loading)

        viewModel = ProfileViewModel(
            getProfileUseCase,
            logoutUseCase,
            updateProfileUseCase,
            updateKycUseCase,
            updatePasswordUseCase,
            deleteAccountUseCase,
            sessionManager
        )
    }

    @After
    fun tearDown() {
        Dispatchers.resetMain()
    }

    @Test
    fun `when load profile successful, state should have partner data`() = runTest {
        val partnerResponse = mockk<PartnerResponse>(relaxed = true)
        every { partnerResponse.name } returns "Test Partner"
        coEvery { getProfileUseCase() } returns flowOf(
            ResultState.Loading,
            ResultState.Success(partnerResponse)
        )

        viewModel.processIntent(ProfileIntent.LoadProfile)

        assertEquals(partnerResponse, viewModel.state.value.partner)
        assertEquals(false, viewModel.state.value.isLoading)
        verify { sessionManager.savePartnerName("Test Partner") }
    }

    @Test
    fun `when update profile successful, state should be updated`() = runTest {
        val data = mapOf("name" to "New Name")
        val partnerResponse = mockk<PartnerResponse>(relaxed = true)
        every { partnerResponse.name } returns "New Name"
        coEvery { updateProfileUseCase(data) } returns flowOf(
            ResultState.Loading,
            ResultState.Success(partnerResponse)
        )

        viewModel.processIntent(ProfileIntent.UpdateProfile(data))

        assertEquals(partnerResponse, viewModel.state.value.partner)
        verify { sessionManager.savePartnerName("New Name") }
    }

    @Test
    fun `when logout successful, should emit logout success effect`() = runTest {
        coEvery { logoutUseCase() } returns flowOf(
            ResultState.Loading,
            ResultState.Success("Logged out")
        )

        viewModel.effect.test {
            viewModel.processIntent(ProfileIntent.Logout)
            assertEquals(ProfileEffect.LogoutSuccess, awaitItem())
        }
    }
}
