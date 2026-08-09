package com.nusatim.partner.features.profile.ui.marketing

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import com.nusatim.partner.core.model.dto.MarketingMaterialResponse
import com.nusatim.partner.features.profile.databinding.ItemMarketingHeaderBinding
import com.nusatim.partner.features.profile.databinding.ItemMarketingMaterialBinding

sealed class MarketingListItem {
    data class Header(val title: String) : MarketingListItem()
    data class Item(val material: MarketingMaterialResponse) : MarketingListItem()
}

class MarketingAdapter(
    private val onItemClick: (MarketingMaterialResponse) -> Unit,
    private val onActionClick: (MarketingMaterialResponse) -> Unit
) : ListAdapter<MarketingListItem, RecyclerView.ViewHolder>(DiffCallback()) {

    override fun getItemViewType(position: Int): Int {
        return when (getItem(position)) {
            is MarketingListItem.Header -> TYPE_HEADER
            is MarketingListItem.Item -> TYPE_ITEM
        }
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): RecyclerView.ViewHolder {
        val inflater = LayoutInflater.from(parent.context)
        return if (viewType == TYPE_HEADER) {
            HeaderViewHolder(ItemMarketingHeaderBinding.inflate(inflater, parent, false))
        } else {
            ItemViewHolder(ItemMarketingMaterialBinding.inflate(inflater, parent, false))
        }
    }

    override fun onBindViewHolder(holder: RecyclerView.ViewHolder, position: Int) {
        val item = getItem(position)
        if (holder is HeaderViewHolder && item is MarketingListItem.Header) {
            holder.bind(item)
        } else if (holder is ItemViewHolder && item is MarketingListItem.Item) {
            holder.bind(item)
        }
    }

    inner class HeaderViewHolder(private val binding: ItemMarketingHeaderBinding) : RecyclerView.ViewHolder(binding.root) {
        fun bind(header: MarketingListItem.Header) {
            binding.root.text = header.title.uppercase()
        }
    }

    inner class ItemViewHolder(private val binding: ItemMarketingMaterialBinding) : RecyclerView.ViewHolder(binding.root) {
        init {
            binding.root.setOnClickListener {
                val pos = adapterPosition
                if (pos != RecyclerView.NO_POSITION) {
                    val item = getItem(pos)
                    if (item is MarketingListItem.Item) onItemClick(item.material)
                }
            }
            binding.ivAction.setOnClickListener {
                val pos = adapterPosition
                if (pos != RecyclerView.NO_POSITION) {
                    val item = getItem(pos)
                    if (item is MarketingListItem.Item) onActionClick(item.material)
                }
            }
        }

        fun bind(item: MarketingListItem.Item) {
            val material = item.material
            binding.tvTitle.text = material.title
            binding.tvDescription.text = material.description ?: ""
            binding.tvDescription.visibility = if (material.description.isNullOrEmpty()) View.GONE else View.VISIBLE
            
            val typeIcon = if (material.isFileBased) {
                when {
                    material.downloadUrl?.endsWith(".pdf") == true -> android.R.drawable.ic_menu_agenda
                    material.downloadUrl?.contains("video") == true -> android.R.drawable.ic_media_play
                    else -> android.R.drawable.ic_menu_gallery
                }
            } else {
                android.R.drawable.ic_menu_edit
            }
            binding.ivTypeIcon.setImageResource(typeIcon)
            
            val actionIcon = if (material.isFileBased) {
                android.R.drawable.stat_sys_download
            } else {
                android.R.drawable.ic_menu_save // Copy icon
            }
            binding.ivAction.setImageResource(actionIcon)
        }
    }

    class DiffCallback : DiffUtil.ItemCallback<MarketingListItem>() {
        override fun areItemsTheSame(oldItem: MarketingListItem, newItem: MarketingListItem): Boolean {
            return if (oldItem is MarketingListItem.Header && newItem is MarketingListItem.Header) {
                oldItem.title == newItem.title
            } else if (oldItem is MarketingListItem.Item && newItem is MarketingListItem.Item) {
                oldItem.material.id == newItem.material.id
            } else false
        }

        override fun areContentsTheSame(oldItem: MarketingListItem, newItem: MarketingListItem): Boolean {
            return oldItem == newItem
        }
    }

    companion object {
        private const val TYPE_HEADER = 0
        private const val TYPE_ITEM = 1
    }
}
