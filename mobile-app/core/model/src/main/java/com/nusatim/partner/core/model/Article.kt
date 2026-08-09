/**
 * Created by Muh. Arifandi on 12/05/2026
 * Email : arif76440@gmail.com
 * Project : My Application
 * Module : core:model
 * File : Article.kt
 *
 * Description:
 * Data class yang merepresentasikan entitas Berita (Article) dalam aplikasi.
 */

package com.nusatim.partner.core.model

import kotlinx.serialization.Serializable

@Serializable
data class Article(
    val author: String? = null,
    val title: String? = null,
    val description: String? = null,
    val url: String,
    val urlToImage: String? = null,
    val publishedAt: String? = null,
    val content: String? = null,
    val sourceName: String? = null
)
