package com.nusatim.partner.core.network.util

/**
 * Created by Muh. Arifandi on 07/05/26.
 * Email : arif76440@gmail.com
 * Project: My Application
 * File: ApiException
 */
class ApiException(
    override val message: String
) : Exception(message)