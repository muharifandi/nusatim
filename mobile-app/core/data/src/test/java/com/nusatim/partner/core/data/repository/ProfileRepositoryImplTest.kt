package com.nusatim.partner.core.data.repository

import app.cash.turbine.test
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.network.ProfileApiService
import com.nusatim.partner.core.model.dto.BaseResponse
import com.nusatim.partner.core.model.dto.PartnerResponse
import io.mockk.coEvery
import io.mockk.mockk
import kotlinx.coroutines.test.runTest
import org.junit.Assert.assertEquals
import org.junit.Before
import org.junit.Test

class ProfileRepositoryImplTest {

    private lateinit var apiService: ProfileApiService
    private lateinit var repository: ProfileRepositoryImpl

    @Before
    fun setUp() {
        apiService = mockk()
        repository = ProfileRepositoryImpl(apiService)
    }

    @Test
    fun `getProfile should emit loading and then success when api call is successful`() = runTest {
        // Arrange
        val partnerResponse: PartnerResponse = mockk()
        val baseResponse = BaseResponse(data = partnerResponse)
        coEvery { apiService.getProfile() } returns baseResponse

        // Act & Assert
        repository.getProfile().test {
            assertEquals(ResultState.Loading, awaitItem())
            val success = awaitItem() as ResultState.Success<PartnerResponse>
            assertEquals(partnerResponse, success.data)
            awaitComplete()
        }
    }

    @Test
    fun `getProfile should emit loading and then error when api call fails`() = runTest {
        // Arrange
        coEvery { apiService.getProfile() } throws Exception("Profile Error")

        // Act & Assert
        repository.getProfile().test {
            assertEquals(ResultState.Loading, awaitItem())
            val error = awaitItem() as ResultState.Error
            assertEquals("Profile Error", error.message)
            awaitComplete()
        }
    }
}
