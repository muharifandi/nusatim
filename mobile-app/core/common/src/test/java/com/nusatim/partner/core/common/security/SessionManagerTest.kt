package com.nusatim.partner.core.common.security

import io.mockk.every
import io.mockk.mockk
import io.mockk.verify
import org.junit.Assert.assertEquals
import org.junit.Before
import org.junit.Test

class SessionManagerTest {

    private lateinit var securityProvider: SecurityProvider
    private lateinit var sessionManager: SessionManager

    @Before
    fun setUp() {
        securityProvider = mockk(relaxed = true)
        sessionManager = SessionManager(securityProvider)
    }

    @Test
    fun `saveAuthToken should call securityProvider saveString`() {
        sessionManager.saveAuthToken("token123")
        verify { securityProvider.saveString("auth_token", "token123") }
    }

    @Test
    fun `getAuthToken should return value from securityProvider`() {
        every { securityProvider.getString("auth_token") } returns "token123"
        assertEquals("token123", sessionManager.getAuthToken())
    }

    @Test
    fun `isUserLoggedIn should return true when token exists`() {
        every { securityProvider.getString("auth_token") } returns "token123"
        assertEquals(true, sessionManager.isUserLoggedIn())
    }

    @Test
    fun `isUserLoggedIn should return false when token is null`() {
        every { securityProvider.getString("auth_token") } returns null
        assertEquals(false, sessionManager.isUserLoggedIn())
    }
}
