package com.nusatim.partner.core.data.repository

import app.cash.turbine.test
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.network.AuthApiService
import com.nusatim.partner.core.model.dto.BaseResponse
import com.nusatim.partner.core.model.dto.PartnerResponse
import io.mockk.coEvery
import io.mockk.mockk
import kotlinx.coroutines.test.runTest
import okhttp3.MultipartBody
import okhttp3.RequestBody
import org.junit.Assert.assertEquals
import org.junit.Before
import org.junit.Test

class AuthRepositoryImplTest {

    private lateinit var apiService: AuthApiService
    private lateinit var repository: AuthRepositoryImpl

    @Before
    fun setUp() {
        apiService = mockk()
        repository = AuthRepositoryImpl(apiService)
    }

    @Test
    fun `register should emit loading and then success when api call is successful`() = runTest {
        // Arrange
        val nameBody: RequestBody = mockk()
        val emailBody: RequestBody = mockk()
        val passwordBody: RequestBody = mockk()
        val passwordConfirmBody: RequestBody = mockk()
        val profilePhotoPart: MultipartBody.Part = mockk()
        val ktpPart: MultipartBody.Part = mockk()
        val npwpPart: MultipartBody.Part? = null
        val bankNameBody: RequestBody = mockk()
        val bankAccountNumBody: RequestBody = mockk()
        val bankAccountHolderBody: RequestBody = mockk()
        val agreementBody: RequestBody = mockk()

        val partnerResponse: PartnerResponse = mockk()
        val baseResponse = BaseResponse(data = partnerResponse)

        coEvery {
            apiService.register(any(), any(), any(), any(), any(), any(), any(), any(), any(), any(), any())
        } returns baseResponse

        // Act & Assert
        repository.register(
            nameBody, emailBody, passwordBody, passwordConfirmBody,
            profilePhotoPart, ktpPart, npwpPart,
            bankNameBody, bankAccountNumBody, bankAccountHolderBody,
            agreementBody
        ).test {
            assertEquals(ResultState.Loading, awaitItem())
            val success = awaitItem() as ResultState.Success<PartnerResponse>
            assertEquals(partnerResponse, success.data)
            awaitComplete()
        }
    }

    @Test
    fun `register should emit loading and then error when api call fails`() = runTest {
        // Arrange
        coEvery {
            apiService.register(any(), any(), any(), any(), any(), any(), any(), any(), any(), any(), any())
        } throws Exception("Network Error")

        // Act & Assert
        repository.register(
            mockk(), mockk(), mockk(), mockk(),
            mockk(), mockk(), null,
            mockk(), mockk(), mockk(),
            mockk()
        ).test {
            assertEquals(ResultState.Loading, awaitItem())
            val error = awaitItem() as ResultState.Error
            assertEquals("Network Error", error.message)
            awaitComplete()
        }
    }
}
